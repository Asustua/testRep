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
    [toc]
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
[color=d3d]test[/color]
[h2]aaaaaaaaaaaaa[/h2]
[h3]xxxxxx[/h3]
[h3]wwwwwww[/h3]
[h3]xeeeeeee[/h3]
[h2]zzzzzzzzzzzzzz[/h2]
[h3]ooooooooo[/h3]
[url]http://www.test.com[/url]
[url=http://www.test.com]aaaa[/url]
[url=/test/aaaa]zzzz[/url]
[div float=right align=left width=80%]qwetrteqrqw
[/div]

[div float=right]213213213123123123
[/div]

[div]----------------------
[/div]

[div width=100px]----------------------
[/div]

[anchor=top]
aaaa[sub]z[/sub][sup]x[/sup]

[table float=right align=center]
[tr]
[td colspan=2]test[/td][td rowspan=2]azzaaz[/td]
[/tr][tr]
[td]test 3[/td][td]test 4[/td]
[/tr]
[/table]

';
$oBbcode = new bbcode();
echo $oBbcode->do_bbcode($sString);

class bbcode {
    
    private $toc = array();
    private $toc_sub = array();
    private $toc_position = 0;
    
    private function remove_new_lines() {
        
    }
    
    private function do_headers($aMatches) {
        if(empty($aMatches['toc'])) {
            $this->toc_sub[$this->toc_position] = array();
            $this->toc[$this->toc_position] = $aMatches['text'];
            $this->toc_position++;
        }
        return '<h2>'.$aMatches['text'].'</h2>';
    }
    
    private function do_sub_headers($aMatches) {
        if(empty($aMatches['toc']))    $this->toc_sub[($this->toc_position - 1)][] = $aMatches['text'];
        return '<h3>'.$aMatches['text'].'</h3>';
    }
    
    private function do_toc($aMatches) {
        $sRet = '';
        $sRet .= '<ul>'.PHP_EOL;
        foreach($this->toc as $iKey => $sHeader) {
            $sRet .= '<li>'.PHP_EOL;
            $sRet .= $sHeader;
            $sRet .= '</li>'.PHP_EOL;
            if(!empty($this->toc_sub[$iKey])) {
                $sRet .= '<ul class="sub_headers">'.PHP_EOL;
                foreach($this->toc_sub[$iKey] as $sSub_header) {
                    $sRet .= '<li>'.PHP_EOL;
                    $sRet .= $sSub_header;
                    $sRet .= '</li>'.PHP_EOL;
                }
                $sRet .= '</ul>'.PHP_EOL;
            }
        }
        $sRet .= '</ul>'.PHP_EOL;
        
        return $sRet;
    }

    private function do_url($aMatches) {
        $sRet = '';
        
        // some tests maybe proxy?
        
        if(!empty($aMatches['text'])) {
            $sRet = '<a href="'.$aMatches['url'].'">'.$aMatches['text'].'</a>';
        } else {
            $sRet = '<a href="'.$aMatches['url'].'">'.$aMatches['url'].'</a>';
        }
        return $sRet;
    }
    
    private function do_div($aMatches) {
        $sRet = '';
        $sStyle = '';
        
        $aStyle = array();
        if(!empty($aMatches['float'])) {
            $aStyle[] = 'float: '.$aMatches['float'];
        }
        
        if(!empty($aMatches['width'])) {
            $aStyle[] = 'width: '.$aMatches['width'];
        }
        
        if(!empty($aMatches['align'])) {
            $aStyle[] = 'text-align: '.$aMatches['align'];
        }
        
        if(!empty($aStyle)) {
            $sStyle = 'style="'.implode(';', $aStyle).'"';
        }
        
        $sRet = '<div '.$sStyle.' >';
        
        return $sRet;
    }
    
    private function do_td($aMatches) {
        $sRet = '';
        
        $sRet .= '<td ';
        if(!empty($aMatches['rowspan'])) {
            $sRet .= ' rowspan="'.$aMatches['rowspan'].'"';
        }
        if(!empty($aMatches['colspan'])) {
            $sRet .= ' colspan="'.$aMatches['colspan'].'"';
        }
        $sRet .= '>';
        
        return $sRet;
    }
    
    private function do_table($aMatches) {
        $sRet = '';
        
        $sStyle = '';
        
        $aStyle = array();
        if(!empty($aMatches['float'])) {
            $aStyle[] = 'float: '.$aMatches['float'];
        }
        
        if(!empty($aMatches['width'])) {
            $aStyle[] = 'width: '.$aMatches['width'];
        }
        
        if(!empty($aMatches['align'])) {
            $aStyle[] = 'text-align: '.$aMatches['align'];
        }
        
        if(!empty($aStyle)) {
            $sStyle = 'style="'.implode(';', $aStyle).'"';
        }
        
        $sRet .= '<table '.$sStyle.'>';
        
        return $sRet;
    }

    public function do_bbcode($sText) {
        
        // $sMsg = nl2br($sMsg);
        $aText = explode(PHP_EOL, $sText);
        
        foreach($aText as $iKey => $sMsg) {
            if(empty($sMsg))                continue;
            // [b] -> strong
            // [h2] -> list element
            $sMsg = preg_replace_callback('!\[h2(?<toc> toc=false|)\](?<text>.*?)\[/h2\]!is', array($this,'do_headers'), $sMsg);
            
            // [h3] -> list element
            $sMsg = preg_replace_callback('!\[h3(?<toc> toc=false|)\](?<text>.*?)\[/h3\]!is', array($this,'do_sub_headers'), $sMsg);

            $aText[$iKey] = $sMsg;
        }
        
        $sText = implode('<br>', $aText);
        
        $sText = str_replace('[b]','<strong>', $sText);
        $sText = str_replace('[/b]','</strong>', $sText);

        // [u] -> underline
        $sText = str_replace('[u]','<span class="underline">', $sText);
        $sText = str_replace('[/u]','</span>', $sText);

        // [i] -> italic
        $sText = str_replace('[i]','<span class="italic">', $sText);
        $sText = str_replace('[/i]','</span>', $sText);

        // [s] -> strike
        $sText = str_replace('[s]','<span class="strike">', $sText);
        $sText = str_replace('[/s]','</span>', $sText);

        // [insert] -> insert
        $sText = str_replace('[ins]','<ins>', $sText);
        $sText = str_replace('[/ins]','</ins>', $sText);

        // [del] -> deleted text
        $sText = str_replace('[del]','<del>', $sText);
        $sText = str_replace('[/del]','</del>', $sText);

        // [ul] -> list
        $sText = str_replace('[ul]','<ul>', $sText);
        $sText = str_replace('[/ul]','</ul>', $sText);

        // [ol] -> list
        $sText = str_replace('[ol]','<ol>', $sText);
        $sText = str_replace('[/ol]','</ol>', $sText);

        // [li] -> list element
        $sText = str_replace('[li]','<li>', $sText);
        $sText = str_replace('[/li]','</li>', $sText);
        
        // [small] -> list element
        $sText = str_replace('[small]','<small>', $sText);
        $sText = str_replace('[/small]','</small>', $sText);
        
        // [small] -> list element
        $sText = str_replace('[sup]','<sup>', $sText);
        $sText = str_replace('[/sup]','</sup>', $sText);
        
        // [sub] -> list element
        $sText = str_replace('[sub]','<sub>', $sText);
        $sText = str_replace('[/sub]','</sub>', $sText);
        
        // [hr] -> line
        $sText = str_replace('[line]','<hr>', $sText);
        $sText = str_replace('[break]','<br>', $sText);
        
        // colour exp. [color=f4f][/color]
        $sText = preg_replace('!\[color=([0-9a-fA-F]{3}|[0-9a-fA-F]{6})\](.*?)\[/color\]!is', '<span style="color: #$1">$2</span>', $sText);
        
        // anchor
        $sText = preg_replace('!\[anchor=([0-9a-zA-Z]+)\]!is', '<a name="$1"></a>', $sText);
        
        
        /* Tables */
        $sText = preg_replace_callback('!\[table( float=(?<float>left|right)|)( align=(?<align>left|center|right)|)( width=(?<width>[0-9px%]{0,8})|)\]!is',array($this, 'do_table'), $sText);
        $sText = str_replace('[/table]','</table>', $sText);
        
        $sText = str_replace('[tr]','<tr>', $sText);
        $sText = str_replace('[/tr]','</tr>', $sText);
        
        $sText = preg_replace_callback('!\[td( rowspan=(?<rowspan>[0-9]+)|)( colspan=(?<colspan>[0-9]+)|)\]!is',array($this, 'do_td'), $sText);
        $sText = str_replace('[/td]','</td>', $sText);
        
        // div
        /////////// float, align, width
        // ( float=(?<float>left|right)|)( align=(?<align>left|center|right)|)( width=(?<width>[0-9px%]{0,8})|)
        ///////////
        $sText = preg_replace_callback('!\[div( float=(?<float>left|right)|)( align=(?<align>left|center|right)|)( width=(?<width>[0-9px%]{0,8})|)\]!is', array($this,'do_div'), $sText);
        $sText = str_replace('[/div]','</div>', $sText);
        
        // colour exp. [color=f4f][/color]
        $sText = preg_replace_callback('#\[url\](?<url>(.*?))\[/url\]#s', array($this, 'do_url'), $sText);
        $sText = preg_replace_callback('#\[url=(?<url>[^\[]+?)\](?<text>.*?)\[/url\]#s', array($this, 'do_url'), $sText);
        
        $sText = preg_replace_callback('!\[toc\]!is', array($this, 'do_toc'), $sText);
        
        return $sText;
    }
    
}
