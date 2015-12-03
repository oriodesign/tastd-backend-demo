<?php

namespace Tastd\Bundle\CoreBundle\Command;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Formatter\OutputFormatterStyle;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator;
use Tastd\Bundle\CoreBundle\Entity\Review;
use Tastd\Bundle\CoreBundle\Repository\ReviewRepository;

/**
 * Class ReviewHandleCommand
 *
 * @package Tastd\Bundle\CoreBundle\Command
 */
class ReviewHandleCommand extends Command
{

    protected $reviewRepository;
    protected $entityManager;

    /**
     * @param ReviewRepository $reviewRepository
     * @param EntityManager    $entityManager
     */
    public function __construct(ReviewRepository $reviewRepository, EntityManager $entityManager)
    {
        parent::__construct();
        $this->reviewRepository = $reviewRepository;
        $this->entityManager = $entityManager;
    }

    /**
     * configure
     */
    protected function configure()
    {
        $this
            ->setName('tastd:review:handle')
            ->setDescription('Handle Reviews');
    }

    /**
     * @param InputInterface  $input
     * @param OutputInterface $output
     *
     * @return int|null|void
     */
    public function execute(InputInterface $input, OutputInterface $output)
    {
        $page = 1;
        $request = new Request();
        $pager = $this->reviewRepository->getAllReviewsPager($request);
        do {
            $pager->setCurrentPage($page);
            /** @var Review $review */
            foreach ($pager->getCurrentPageResults() as $review) {
                $this->cleanComment($review);
            }
            $page++;
            $this->entityManager->flush();
        } while ($pager->hasNextPage());
    }

    /**
     * @param Review $review
     */
    protected function cleanComment(Review $review) {
        $comment = $review->getComment();
        $pattern = '/(#\w+)/';
        $replacement = '';
        $comment = preg_replace($pattern, $replacement, $comment);
        $comment = preg_replace('!\s+!', ' ', $comment);
        $review->setComment($comment);
    }


}