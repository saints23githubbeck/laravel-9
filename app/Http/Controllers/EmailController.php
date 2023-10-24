<?php

namespace App\Http\Controllers;

use App\Http\Requests\ValidateRequestBody;
use App\Jobs\SendEmailJob;
use App\Utilities\Contracts\ElasticsearchHelperInterface;
use App\Utilities\Contracts\RedisHelperInterface;

use Illuminate\Http\Request;
use Elasticsearch\ClientBuilder;
class EmailController extends Controller
{
    // TODO: finish implementing send method

    protected $elasticsearchHelper;
    protected $elasticsearch;

    public function __construct(ElasticsearchHelperInterface $elasticsearchHelper)
    {
        $this->elasticsearchHelper = $elasticsearchHelper;
        $this->elasticsearch = ClientBuilder::create()->setHosts(config('elasticsearch.connections.default.hosts'))->build();

    }
    public function send(Request $request)
    {

        $toEmailAddresses = array($request->input('toEmailAddress'));
        $messageSubject = $request->input('messageSubject');
        $messageBody = $request->input('messageBody');

        if ($toEmailAddresses== [""] || $messageBody == "" || $messageSubject == ""){
            return response()->json(['message' => 'All field required',422]);
        }

        /** @var ElasticsearchHelperInterface $elasticsearchHelper */
//        $elasticsearchHelper = app()->make(ElasticsearchHelperInterface::class);
        foreach ($toEmailAddresses as $toEmailAddress) {
//            return  $toEmailAddress;
            SendEmailJob::dispatch($toEmailAddress, $messageSubject, $messageBody)->onQueue('emails');

            // Store email information in Elasticsearch
            $this->elasticsearchHelper->storeEmail($messageBody, $messageSubject, $toEmailAddress);

        }

        return response()->json(['message' => 'Emails sent successfully',200]);
    }

    //  TODO - BONUS: implement list method
    public function list()
    {
        $params = [
            'index' => 'bulkEmails',
            'body' => [
                'query' => [
                    'match_all' => new \stdClass(),
                ],
            ],
        ];

        $response = $this->elasticsearch->search($params);

        $respondData = $response['data']['data'];

        //  return the record from the Elasticsearch response
        return  $respondData;
    }
}
