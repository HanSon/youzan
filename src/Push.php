<?php


namespace Hanson\Youzan;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class Push
{

    /**
     * @var Request
     */
    private $request;
    private $clientId;
    private $secret;

    public function __construct($clientId, $secret, Request $request)
    {
        $this->clientId = $clientId;
        $this->secret = $secret;
        $this->request = $request;
    }

    /**
     * @return Response|array
     * @throws YouzanException
     */
    public function parse()
    {
        $data = $this->request->getContent();
        
        $data = json_decode($data, true);

        if ($this->checkTest($data)) {
            return false;
        }

        $this->checkSign($data);

        if (isset($data['msg'])) {
            $data['msg'] = json_decode(urldecode($data['msg']), true);
        }

        return $data;
    }

    public function checkTest($data)
    {
        return $data['test'] ?? false === true;
    }

    public function checkSign($data)
    {
        $sign = md5($this->clientId . ($data['msg'] ?? '') . $this->secret);

        if($sign != $data['sign']){
            throw new YouzanException('签名不正确');
        }
    }

    public function response()
    {
        return Response::create(json_encode(['code' => 0, 'msg' => 'success']));
    }

}
