<?php

namespace AdventureGame\Response;

class ResponseController
{
    public function __construct(private Response $response)
    {
    }

    public function getResponse(): Response
    {
        return $this->response;
    }
}