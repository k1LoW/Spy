<?php

App::uses('Component', 'Controller');

/**
 * SpyComponent.
 */
class SpyComponent extends Component
{
    public $sessionBaseKey = 'Spy';
    public $spyRequestLimit = 15;
    public $Controller;

    public $components = array('Session');

    /**
     * startup
     */
    public function startup(Controller $controller)
    {
        $this->Controller = $controller;
        $this->spyRequest();
        $this->spyAuthUser();
    }

    /**
     * spyRequest
     *
     */
    public function spyRequest()
    {
        $request = [
            'method' => env('REQUEST_METHOD'),
            'url' => Router::url( $this->Controller->request->here(true), true ),
            'timestamp' => date('Y-m-d H:i:s'),
        ];
        $requests = $this->getData('requests');
        if (empty($requests)) {
            $requests = [];
        }
        $count = array_unshift($requests, $request);
        while ($count > $this->spyRequestLimit) {
            array_pop($requests);
            $count--;
        }
        $this->setData('requests', $requests);
    }

    /**
     * spyAuthUser
     *
     */
    public function spyAuthUser(){
        if (empty($this->Controller->Auth)) {
            return;
        }
        if (empty($this->Controller->Auth->authenticate)) {
            return;
        }
        $userModel = null;
        foreach (array('all','Form') as $key) {
            if (!empty($this->Controller->Auth->authenticate[$key]['userModel'])) {
                $userModel = $this->Controller->Auth->authenticate[$key]['userModel'];
            }
        }
        if (empty($userModel)) {
            return;
        }

        $userId = $this->Controller->Auth->user('id');
        if (empty($userId)) {
            $this->deleteData('authUser.' . $userModel);
            return;
        }

        $spied = $this->getData('authUser.' . $userModel);
        if (!empty($spied)) {
            return;
        }

        $data = [
            'user_id' => $userId,
            'user_model' => $userModel,
            'client_ip' => $this->Controller->request->clientIp(false),
            'referer' => $this->Controller->request->referer(false),
            'user_agent' => env('HTTP_USER_AGENT'),
            'timestamp' => date('Y-m-d H:i:s'),
        ];

        $SpiedAuthUser = ClassRegistry::init('Spy.SpiedAuthUser');
        $SpiedAuthUser->create();
        $SpiedAuthUser->save($data);
        $this->setData('authUser.' . $userModel, $data);
        return;
    }

    /**
     * getData
     *
     */
    public function getData($key)
    {
        return $this->Session->read($this->sessionBaseKey .'.'. $key);
    }

    /**
     * setData
     *
     */
    public function setData($key, $data)
    {
        return $this->Session->write($this->sessionBaseKey .'.'. $key, $data);
    }

    /**
     * deleteData
     *
     */
    public function deleteData($key)
    {
        return $this->Session->delete($this->sessionBaseKey .'.'. $key);
    }
}
