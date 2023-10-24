<?php


namespace App\Utilities\Contracts;

use App\Models\BulkMail;
use Elasticsearch\ClientBuilder;


class ElasticsearchHelper implements ElasticsearchHelperInterface
{

    /**
     * Store the email's message body, subject and to address inside elasticsearch.
     *
     * @param  string  $messageBody
     * @param  string  $messageSubject
     * @param  array  $toEmailAddress
     * @return mixed - Return the id of the record inserted into Elasticsearch
     */
    protected $elasticsearch;

    public function __construct()
    {
        $this->elasticsearch = ClientBuilder::create()->setHosts(config('elasticsearch.connections.default.hosts'))->build();
    }

    public function storeEmail(string $messageBody, string $messageSubject, array $toEmailAddress): mixed
    {
        if (is_array($toEmailAddress)) {
            // Convert the array to a JSON string
            $toEmailAddressd = json_encode($toEmailAddress);

        }

        // Assuming you have an BulkMail model for Elasticsearch
        $email = new BulkMail();
        $email->toEmailAddress = $toEmailAddressd;
        $email->messageSubject = $messageSubject;
        $email->messageBody = $messageBody;
        $email->save();

        // Index the  data in Elasticsearch
        $params = [
            'index' => 'your_index_name',
            'type' => '_doc',
            'id' => $email->id,
            'body' => [
                'toEmailAddress' => $email->toEmailAddress,
                'messageSubject' => $email->messageSubject,
                'messageBody' => $email->messageBody,
            ]
        ];

        $response = $this->elasticsearch->index($params);
        $recentMessageData = [
                'id' => $response['_id'],
                'messageBody' => $messageBody,
                'toEmailAddress' => $toEmailAddress,
            ];

//            /** @var RedisHelperInterface $redisHelper */
            $redisHelper = app()->make(RedisHelperInterface::class);

        $redisHelper->storeRecentMessage($recentMessageData);
        return [
            'id' => $email->id,
            'elasticsearch_id' => $response['_id']
        ];
    }
}
