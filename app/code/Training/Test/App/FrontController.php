<?php

namespace Training\Test\App;

use Magento\Framework\App\RequestInterface;
use Magento\Framework\App\RouterListInterface;
use Magento\Framework\App\ResponseInterface;
use Psr\Log\LoggerInterface;

/**
 * This class saves available routers into a log file
 */
class FrontController extends \Magento\Framework\App\FrontController
{
    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param RouterListInterface $routerList
     * @param ResponseInterface $response
     * @param LoggerInterface $logger
     */
    public function __construct(
        RouterListInterface $routerList,
        ResponseInterface $response,
        LoggerInterface $logger
    ){
        $this->logger = $logger;
        parent::__construct($routerList, $response);
    }

    /**
     * @param RequestInterface $request
     * @return ResponseInterface|\Magento\Framework\Controller\ResultInterface|null
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function dispatch(RequestInterface $request)
    {
        $this->logger->info( PHP_EOL . "Magento2 Routers List:" . PHP_EOL);
        foreach ($this->_routerList as $router) {
            $this->logger->info(get_class($router). PHP_EOL);
        }
        return parent::dispatch($request);
    }
}
