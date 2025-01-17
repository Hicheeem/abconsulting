<?php namespace CodeIgniter\Filters\fixtures;

use CodeIgniter\Filters\FilterInterface;
use CodeIgniter\HTTP\RequestInterface;
use CodeIgniter\HTTP\ResponseInterface;

class GoogleMe implements FilterInterface
{
	public function before(RequestInterface $request)
	{
		$request->url = 'http://google.com';

		return $request;
	}

	//--------------------------------------------------------------------

	public function after(RequestInterface $request, ResponseInterface $response)
	{
		$response->csp = 'http://google.com';

		return $response;
	}

	//--------------------------------------------------------------------
}
