<?php

/*
 * Sometime too hot the eye of heaven shines
 */

namespace Pad\Foundation\Socket\Observers;

use Pad\Foundation\Interfaces\ObserverInterface;
use Pad\Models\Pad;

class Deliver implements ObserverInterface
{

    protected $pad;

    
    public function __construct()
    {
        $this->pad = new Pad();
    }

    public function handle($data)
    {
        list($message, $sender, $socketServer) = $data;

        $insert = json_encode($message->insert);
        $content = json_encode($message->content);
        $padId = $message->pad_id;

        $this->deliverMessage($sender, $insert, $content, $padId, $socketServer);
    }


    protected function deliverMessage($sender, $insert, $content, $padId, $socketServer)
    {
        $members = $this->pad->getMembersById($padId);

        $message = '{"insert":'.$insert.',"pad_id":"'.$padId.'"}';

        foreach ($members as $key => $member) {
            if ($member['user'] != $sender) {
               $socketServer->push($member['user'], $message);
            }
        }
    }
}