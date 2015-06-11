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
    
        
    public function getComments($nId, $sSwitch) {
        // user => avatar, color, id
        $sQuery = '
            SELECT
                comments.id,
                comments.date,
                comments.text,
                users.username,
                comments_votes.id AS voted,
                (
                    SELECT
                        COUNT(*)
                    FROM
                        comments AS c
                    WHERE
                        c.switch = "c"
                        AND c.i_id = comments.id
                ) AS additional_comments,
                (
                    SELECT
                        SUM(comments_votes)
                    FROM
                        comments_votes
                    WHERE
                        comments_votes.comments_id = comments.id
                ) AS comments_vote
            FROM
                comments
            LEFT JOIN
                users ON users.user_id = comments.user_id
            LEFT JOIN
                comments_votes ON comments_votes.user_id = '.$this->user->data['user_id'].'
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
                        comments.text,
                        comments_votes.id AS voted,
                        (
                            SELECT
                                SUM(comments_votes)
                            FROM
                                comments_votes
                            WHERE
                                comments_votes.comments_id = comments.id
                        ) AS comments_vote
                    FROM
                        comments
                    LEFT JOIN
                        users ON users.user_id = comments.user_id
                    LEFT JOIN
                        comments_votes ON comments_votes.user_id = '.$this->user->data['user_id'].'
                    WHERE
                        comments.switch = "c"
                        AND comments.i_id = '.(int)$aRows['id'];
                
                $oSql_additional = $this->db->sql_query($sQuery);
                
                while($aRows_additional = $this->db->sql_fetchrow($oSql_additional)) {
                    
                    $aAdditional[] = array(
                        'text'  => $aRows_additional['text'],
                        'user'  => stripslashes($aRows_additional['username']),
                        'date'  => '',
                        'comments_vote' => $aRows_additional['comments_vote'],
                        'voted'     => $aRows_additional['voted'],
                    );
                }
            }
            $aComments[] = array(
                'additional'    => $aAdditional,
                'text'          => '',
                'user'  => stripslashes($aRows_additional['username']),
                'comments_vote' => $aRows_additional['comments_vote'],
                'voted'     => $aRows_additional['voted'],
            );
        }
    }
    
    private function refreash() {
        header('Refresh: 0');
        die();
    }
    
    private function makeModel() {
        if(empty($this->request->post['comment_action']) and $this->user->data['user_id'] != ANNONYMOUS && 
                !iBOT) {
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
                    $this->refreash();
                }
            break;
            case 'delete':
                if(iAdmin && !empty($this->request->post['comment_i'])) {
                    $aWhere = array();
                    $aWhere['id'] = $this->request->post['comment_i'];
                    
                    $this->db->delete('comments', $aWhere);
                }
            break;
            case 'vote_up':
                if($this->user->reputation_level(2)) {
                    if(!empty($this->request->post['comment_i'])) {
                        $aInsert = array();
                        $aInsert['user_id'] = $this->user->data['user_id'];
                        $aInsert['comment_id'] = $this->request->post['comment_id'];
                        $aInsert['date'] = date('Y-m-d H:i:s');
                        $aInsert['vote'] = 1;
                        
                        $this->db->insertUpdate('comments_vote', $aInsert);
                    } else {
                        // Error 1. log->
                    }
                } else {
                    // Your Tibia Royal level is too small to make this operation, you can do that from level 2.
                }
            break;
            case 'vote_down':
                if($this->user->reputation_level(4)) {
                    if(!empty($this->request->post['comment_i'])) {
                        $aInsert = array();
                        $aInsert['user_id'] = $this->user->data['user_id'];
                        $aInsert['comment_id'] = $this->request->post['comment_id'];
                        $aInsert['date'] = date('Y-m-d H:i:s');
                        $aInsert['vote'] = -1;
                        
                        $this->db->insertUpdate('comments_vote', $aInsert);
                    } else {
                        // Error 1. log->
                    }
                } else {
                    // Your Tibia Royal level is too small to make this operation, you can do that from level 4.
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
                    
                    $this->refreash();
                }
            break;
        }
        
    }
}
