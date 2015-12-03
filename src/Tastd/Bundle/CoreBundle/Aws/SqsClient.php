<?php

namespace Tastd\Bundle\CoreBundle\Aws;

use Aws\Sqs\SqsClient as BaseSqsClient;
use Guzzle\Service\Resource\Model;

/**
 * Class SqsClient
 *
 * @package Tastd\Bundle\CoreBundle\Aws
 */
class SqsClient
{
    protected $sqs;

    /**
     * @param string $apiKey
     * @param string $apiSecret
     * @param string $queueUrl
     */
    public function __construct($apiKey, $apiSecret, $queueUrl)
    {
        $this->queueUrl = $queueUrl;
        $this->sqs = BaseSqsClient::factory(array(
            'key' => $apiKey,
            'secret' => $apiSecret,
            'region' => 'us-east-1'
        ));
    }

    /**
     * @param string $message
     * @param array  $attributes
     *
     * @return Model
     */
    public function send($message, $attributes = array())
    {
        $this->sqs->sendMessage(array(
            'QueueUrl'     => $this->queueUrl,
            'MessageBody'  => $message,
            'DelaySeconds' => 30,
            'MessageAttributes' => $attributes
        ));
    }

    /**
     * @return Model
     */
    public function receive()
    {
        $result = $this->sqs->receiveMessage(array(
            'QueueUrl' => $this->queueUrl,
        ));

        return $result;
    }

    /**
     * @param string $receiptHandle
     *
     * @return Model
     */
    public function delete($receiptHandle)
    {
        $result = $this->sqs->deleteMessage(array(
            'QueueUrl' => $this->queueUrl,
            'ReceiptHandle' => $receiptHandle,
        ));

        return $result;
    }

}
