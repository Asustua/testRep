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
class Comments {
    private $db;
    private $request;
    private $user;
    
    public function __connstruct($oDb, $oRequest, $oUser) {
        $this->db = $oDb;
        $this->request = $oRequest;
        $this->user = $oUser;
    }
    
    public function getForm($nId, $sSwitch) {
        
    }
    
    public function printComments($nId, $sSwitch) {
        $this->makeModel();
        $aComments = $this->getComments($nId, $sSwitch);
        printCommentsForm($iId, $sSwitch);
        foreach($aComments as $aComment) {
            printComments($aComment);
        }
    }
    
    private function getComments($nId, $sSwitch) {
        $sQuery = '
            SELECT
                comments.text,
                (
                    SELECT
                        COUNT(*)
                    FROM
                        comments AS c
                    WHERE
                        c.switch = "c"
                        AND c.i_id = comments.id
                ) as additional_comments
            FROM
                comments
            WHERE
                comments.switch = '.$this->db->sql_escape($sSwitch).'
                AND comments.i_id = '.(int)$nId;
        
        $oSql = $this->db->sql_query($sQuery);
        if($this->db->sql_affectedrows() == 0)  return 0;
        
        $aComments = array();
        
        while($aRows = $this->db->sql_fetchrow($oSql)) {
            $aAdditional = array();
            if($aRows['additional_comments'] > 0) {
                $sQuery = '
                    SELECT
                        comments.text
                    FROM
                        comments
                    WHERE
                        comments.switch = "c"
                        AND comments.i_id = '.(int)$aRows['id'];
                
                $oSql_additional = $this->db->sql_query($sQuery);
                
                while($aRows_additional = $this->db->sql_fetchrow($oSql_additional)) {
                    $aAdditional[] = array(
                        'text'  => $aRows_additional['text'],
                        'user'  => '',
                        'date'  => ''
                    );
                }
            }
            $aComments[] = array(
                'additional'    => $aAdditional,
                'text'          => ''
            );
        }
    }
    
    private function makeModel() {
        if(empty($this->request->post['comment_i_id']) || empty($this->request->post['comment_i_switch'])
                || empty($this->request->post['comment_action']) || empty($this->request->post['comment_text'])) {
            return;
        }
        
        switch($oRequest->post['comment_action']) {
            case 'insert':
                if(!empty($this->request->post['comment_i_id']) && !empty($this->request->post['comment_i_switch']) && !empty($this->request->post['comment_text'])) {
                    $aInsert = array();
                    $aInsert['text'] = $this->request-post['comment_text'];
                    $aInsert['switch'] = $this->request->post['comment_switch'];
                    $aInsert['i_id'] = $this->request->post['comment_i_id'];

                    $this->db->insert('comments', $aInsert);
                }
            break;
            case 'delete':
                if(iAdmin && !empty($this->request->post['comment_i'])) {
                    $aWhere = array();
                    $aWhere['id'] = $this->request->post['comment_i'];
                    
                    $this->db->delete('comments', $aWhere);
                }
            break;
            case 'edit':
                if(!iAdmin) {
                    $sQuery = '
                        SELECT
                            comments.user_id
                        FROM
                            comments
                        WHERE
                            comments.id = '.(int)$this->request->post['comment_i_id'].'
                        ';
                    $oSql = $oDb->sql_query($sQuery);
                    $aRow = $oDb->sql_fetchrow($oSql);
                    if($aRow['user_id'] != $this->user->data['user_id']) return;
                }
                if(!empty($this->request->post['comment_i']) && !empty($this->request->post['comment_i_id']) && 
                        !empty($this->request->post['comment_i_switch']) && !empty($this->request->post['comment_text'])) {
                    $aInsert = array();
                    $aInsert['text'] = $this->request-post['comment_text'];
                    $aInsert['switch'] = $this->request->post['comment_switch'];
                    $aInsert['i_id'] = $this->request->post['comment_i_id'];
                    
                    $aWhere = array();
                    $aWhere['switch'] = $this->request->post['comment_switch'];
                    $aWhere['i_id'] = $this->request->post['comment_i_id'];
                    $aWhere['id'] = $this->request->post['comment_i'];

                    $oDb->update('comments', $aInsert, $aWhere);
                }
            break;
        }
        
    }
}
