<?php

namespace Tastd\Bundle\CoreBundle\Command;

use Pagerfanta\Pagerfanta;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\ConfirmationQuestion;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Stopwatch\Stopwatch;
use Symfony\Component\Validator\Validator;
use Tastd\Bundle\CoreBundle\Entity\GeoScore;
use Tastd\Bundle\CoreBundle\Entity\User;
use Tastd\Bundle\CoreBundle\Manager\ScoreManager;
use Tastd\Bundle\CoreBundle\Repository\ReviewRepository;
use Tastd\Bundle\CoreBundle\Repository\UserRepository;

/**
 * Class ScoreCalculatorCommand
 *
 * @package Tastd\Bundle\CoreBundle\Command
 */
class ScoreCalculatorCommand extends Command
{

    protected $userRepository;
    protected $reviewRepository;
    protected $scoreManager;
    /** @var InputInterface */
    protected $input;
    protected $output;
    protected $question;
    protected $followerIds;
    protected $stopWatch;

    /**
     * @param UserRepository $userRepository
     * @param ReviewRepository $reviewRepository
     * @param ScoreManager $scoreManager
     */
    public function __construct(
        UserRepository $userRepository,
        ReviewRepository $reviewRepository,
        ScoreManager $scoreManager)
    {
        parent::__construct();
        $this->userRepository = $userRepository;
        $this->reviewRepository = $reviewRepository;
        $this->scoreManager = $scoreManager;
    }

    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('tastd:score:calculator')
            ->setDescription('Update Score for Users and reviews');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $this->input = $input;
        $this->output = $output;
        $userIds = $this->userRepository->getAllIds();
        $this->stopWatch = new Stopwatch();
        $this->stopWatch->start('command');
        foreach ($userIds as $userId) {
            $this->output->writeln('===========');
            $this->output->writeln(sprintf('[USER] %s', $userId));
            $this->followerIds = $this->userRepository->getFollowersIds($userId);
            $this->updateReviewsScore($userId);
            $this->updateGeoScore($userId);
            $this->updateUserScore($userId);
        }
        $event = $this->stopWatch->stop('command');

        $this->output->writeln('===========');
        $this->output->writeln('===========');
        $this->output->writeln(sprintf('Success! Command executed in %sms', $event->getDuration()));
    }

    /**
     * @param $userId
     */
    public function updateReviewsScore($userId)
    {
        $reviews = $this->reviewRepository->getArrayByUserId($userId);
        foreach ($reviews as $review) {
            $this->updateReviewScore($review);
        }
    }

    /**
     * @param $userId
     */
    public function updateGeoScore($userId)
    {
        $geoScoresData = $this->reviewRepository->getGeoScoreArrayByUserId($userId);
        foreach ($geoScoresData as $geoScoreData) {
            $this->scoreManager->deleteGeoScore($userId, $geoScoreData['geoname_id']);
            $this->scoreManager->insertGeoScore(
                $userId,
                $geoScoreData['geoname_id'],
                $geoScoreData['score'],
                $geoScoreData['reviews_count']);
        }
    }

    /**
     * @param $review
     */
    public function updateReviewScore($review)
    {
        $score = $this->scoreManager->calculateReviewScore($review['restaurant_id'], $this->followerIds);
        $this->output->writeln(sprintf('- review %s with score %s', $review['id'], $score));
        $this->scoreManager->updateReviewScore($review['id'], $score);
    }

    /**
     * @param $userId
     */
    public function updateUserScore($userId)
    {

        $reviewsTotalScore = $this->scoreManager->calculateReviewsUserTotalScore($userId);
        $followersScore = $this->getFollowersCountScore();
        $restaurantCountScore = $this->scoreManager->calculateRestaurantCountScore($userId);
        $userTotalScore = $reviewsTotalScore + $followersScore + $restaurantCountScore;
        $userTotalScore = $this->limitScoreForFriends($userTotalScore, $userId);
        $this->output->writeln(sprintf('TOTAL SCORE: %s | reviews %s | followers %s | restaurants %s ',
            $userTotalScore, $reviewsTotalScore, $followersScore, $restaurantCountScore));



        $this->scoreManager->updateUserScore($userId, $userTotalScore);
    }

    /**
     * @param $score
     * @param $userId
     * @return float
     */
    protected function limitScoreForFriends($score, $userId)
    {
        $id = (int) $userId;
        $friendsIds = array(25);
        if (in_array($id, $friendsIds)) {
            $score = (int) $score / 5;
        }

        return $score;
    }

    /**
     * @return int
     */
    protected function getFollowersCountScore()
    {
        return ScoreManager::COUNT_FOLLOWERS_MULTIPLIER * count($this->followerIds);
    }
    

}