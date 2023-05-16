<?php

namespace App\Service;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

interface BaseCRMControllerInterface
{
    /**
     * @param string|null $orderBy
     * @param string|null $direction
     * @return Response
     */
    public function list(?string $orderBy = null, ?string $direction = null): Response;

    /**
     * @param Request $Request
     * @param integer|null $id
     * @return Response
     */
    public function add(Request $Request, ?int $id = null): Response;

    /**
     * @param Request $Request
     * @param integer|null $id
     * @return Response
     */
    public function edit(Request $Request, ?int $id): Response;

    /**
     * @param integer|null $id
     * @return Response
     */
    public function remove(?int $id): Response;
}
