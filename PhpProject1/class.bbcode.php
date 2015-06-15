<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */


/**
 * Description of class
 *
 * @author patryk
 */

$sString = '
[b]aaaa[/b]
[u]qqqq[/u]
[i]eeee[/i]
[del]qqq[/del]
[ins]ewww[/ins]
[ul]
[li]aaaa[/li]
[li]zzzz[/li]
[/ul]
[ol]
[li]aaaa[/li]
[li]zzzz[/li]
[/ol]
[color=333]test[/color]





';

echo bbcode::do_bbcode($sString);

class bbcode {
    
    static function do_bbcode($sMsg) {
        
        // [b] -> strong
        $sMsg = str_replace('[b]','<strong>', $sMsg);
        $sMsg = str_replace('[/b]','</strong>', $sMsg);
        
        // [u] -> underline
        $sMsg = str_replace('[u]','<span class="underline">', $sMsg);
        $sMsg = str_replace('[/u]','</span>', $sMsg);
        
        // [s] -> strike
        $sMsg = str_replace('[s]','<span class="strike">', $sMsg);
        $sMsg = str_replace('[/s]','</span>', $sMsg);
        
        // [insert] -> insert
        $sMsg = str_replace('[ins]','<ins>', $sMsg);
        $sMsg = str_replace('[/ins]','</ins>', $sMsg);
        
        // [del] -> deleted text
        $sMsg = str_replace('[del]','<del>', $sMsg);
        $sMsg = str_replace('[/del]','</del>', $sMsg);
        
        // [ul] -> list
        $sMsg = str_replace('[ul]','<ul>', $sMsg);
        $sMsg = str_replace('[/ul]','</ul>', $sMsg);
        
        // [ol] -> list
        $sMsg = str_replace('[ol]','<ol>', $sMsg);
        $sMsg = str_replace('[/ol]','</ol>', $sMsg);
        
        // [li] -> list element
        $sMsg = str_replace('[li]','<li>', $sMsg);
        $sMsg = str_replace('[/li]','</li>', $sMsg);
        
        // colour exp. [color=f4f][/color]
        $sMsg = preg_replace('!\[color=([0-9a-fA-F]{3}|[0-9a-fA-F]{6}):$uid\](.*?)\[/color:$uid\]!is', '<span style="color: #$1">$2</span>', $sMsg);
        
        
        return $sMsg;
    }
    
}
