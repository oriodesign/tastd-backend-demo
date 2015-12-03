<?php

namespace Tastd\Bundle\CoreBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Validator\Validator;
use Tastd\Bundle\CoreBundle\Entity\User;
use Tastd\Bundle\CoreBundle\Mailer\Mailer;
use Tastd\Bundle\CoreBundle\Manager\ScoreManager;
use Tastd\Bundle\CoreBundle\Repository\ReviewRepository;
use Tastd\Bundle\CoreBundle\Repository\UserRepository;

/**
 * Class RecapEmailCommand
 *
 * @package Tastd\Bundle\CoreBundle\Command
 */
class RecapEmailCommand extends Command
{

    protected $userRepository;
    protected $reviewRepository;
    protected $scoreManager;
    /** @var InputInterface */
    protected $input;
    /** @var OutputInterface  */
    protected $output;
    protected $question;
    protected $followerIds;
    protected $stopWatch;
    protected $mailer;
    protected $csv;
    protected $isCsvEmail;

    /**
     * @param UserRepository   $userRepository
     * @param ReviewRepository $reviewRepository
     * @param ScoreManager     $scoreManager
     * @param Mailer           $mailer
     */
    public function __construct(
        UserRepository $userRepository,
        ReviewRepository $reviewRepository,
        ScoreManager $scoreManager,
        Mailer $mailer)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->reviewRepository = $reviewRepository;
        $this->scoreManager = $scoreManager;
        $this->mailer = $mailer;
        $this->isCsvEmail = false;
        $this->csv = 'userId,email,total,position,restaurantCount,restaurantScore,followersCount,followersScore,sharedScore,sharedCount' . PHP_EOL;
    }

    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('tastd:email:recap')
            ->setDescription('Send recap email to all users')
            ->addArgument('user', InputArgument::OPTIONAL, 'User id')
            ->addOption('csv', null, InputOption::VALUE_NONE, 'Send csv email');
    }

    /**
     * @param InputInterface $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $this->isCsvEmail = $input->getOption('csv');
        $userId = $input->getArgument('user');
        if ($userId) {
            $user = $this->userRepository->get($userId);
            return $this->sendEmail($user);
        }

        $request = new Request();
        $page = 1;
        $pager = $this->userRepository->getUsersPager($request);
        do {
            $pager->setCurrentPage($page);
            /** @var User $user */
            foreach ($pager->getCurrentPageResults() as $user) {
                $this->executeForUser($user);
            }
            $page++;
        } while ($pager->hasNextPage());

        if ($this->isCsvEmail) {
            $this->mailer->sendCsvRecapEmail($this->csv);
        }
    }

    /**
     * @param User $user
     */
    protected function executeForUser(User $user)
    {
        try {
            $this->output->write(sprintf('[%s]: %s',$user->getId(), $user->getEmail()));
            $this->sendEmail($user);
        } catch (\Exception $e) {
            $this->output->writeln($e->getMessage());
        }
    }

    /**
     * @param User $user
     */
    protected function sendEmail(User $user)
    {
        $data = array();
        $data['userId'] = $user->getId();
        $data['email'] = $user->getEmail();
        $data['total'] = $user->getScore();
        $data['position'] = $this->scoreManager->getScorePosition($user->getId());

        $data['restaurantsCount'] = $this->scoreManager->getLastWeekRestaurantsCount($user->getId());
        $data['restaurantsScore'] = $data['restaurantsCount'] * ScoreManager::COUNT_RESTAURANT_MULTIPLIER;

        $data['followersCount'] = $this->scoreManager->getLastWeekFollowersCount($user->getId());
        $data['followersScore'] = $data['followersCount'] * ScoreManager::COUNT_FOLLOWERS_MULTIPLIER;

        $data['sharedScore'] = $this->scoreManager->getLastWeekCommonReviewsCount($user->getId());
        $data['sharedCount'] = $data['sharedScore'] * ScoreManager::COMMON_REVIEW_MULTIPLIER;

        $this->output->write('    ' . json_encode($data) . PHP_EOL);

        if ($this->isCsvEmail) {
            $this->csv .= implode(',', $data) . PHP_EOL;
        } else {
            $this->mailer->sendRecapEmail($user, $data);
        }

    }


}