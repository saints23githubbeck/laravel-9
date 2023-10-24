<?php

namespace App\Utilities\Contracts;

interface ElasticsearchHelperInterface {

    /**
     * Store the email's message body, subject and to address inside elasticsearch.
     *
     * @param  string  $messageBody
     * @param  string  $messageSubject
     * @param  array  $toEmailAddress
     * @return mixed - Return the id of the record inserted into Elasticsearch
     */

    public function storeEmail(string $messageBody, string $messageSubject, array $toEmailAddress): mixed;

}