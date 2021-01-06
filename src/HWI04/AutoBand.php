<?php

namespace HWI04;

use pocketmine\plugin\PluginBase;
use pocketmine\utils\Internet;
use tjwls012\bandapi\BandAPI;
use pocketmine\scheduler\Task;

class AutoBand extends PluginBase
{
    public function onEnable()
    {
        if($this->getServer()->getPluginManager()->getPlugin('BandAPI') === null)
        {
            $this->getLogger()->warning("해당 플러그인을 사용하기 위해서는 BandAPI 가 필요합니다");
            $this->getServer()->getPluginManager()->disablePlugin($this);
        }
		$this->getScheduler()->scheduleRepeatingTask(new CheckTask($this),100);
    }
}
class CheckTask extends Task
{
    public function __construct(AutoBand $owner)
    {
        $this->owner = $owner;
        $this->token = "토큰"; //자신의 토큰을 적어주세요
        $this->key = "밴드키"; //자신의 밴드키를 적어주세요
        $this->api = $owner->getServer()->getPluginManager()->getPlugin("BandAPI");
        $this->comment = "가입인사를 적어주세요 !"; //설정할 가입인사를 적어주세요
    }
	//public $num = 0;
    public function onRun(int $currentTick)
    {
		//$this->num++;
        $url = json_decode(Internet::getURL("https://openapi.band.us/v2/band/posts?access_token=".$this->token."&band_key=".$this->key), true);
        foreach($url["result_data"]["items"] as $post)
        {
            if(strpos($post["content"], "님이 가입했습니다") !== ) //가입글인지 확인합니다
            {
                if(isset($post["latest_comments"])) continue; //댓글이 있는 가입글일 시 , 넘어갑니다
                $this->owner->getLogger()->alert($post["content"]." 라는 글에 댓글을 작성을 시도합니다."); //콘솔에 시도한다는 로그를 남깁니다
                BandAPI::getInstance()->writeComment($this->token,$this->key,$post["post_key"],$this->comment); //밴드에 댓글을 작성합니다
            }
        }
		//$this->owner->getLogger()->notice("{$this->num} 번째 체킹중");
    }
}
