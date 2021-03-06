<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Likes_model extends CI_Model {
    /*
     * POST_UPS
     * post_up_id
     * post_up_user
     * post_up_post
     * 
     * POST_DOWNS
     * post_down_id
     * post_down_user
     * post_down_post
     */

    public function __construct() {
        parent::__construct();
    }

    public function hasUp($postId) {
        if ($this->session->userdata('user_id')) {
            $userId = $this->session->userdata('user_id');
        } else {
            $userId = 35;
        }
        $this->db->where('post_up_post', $postId);
        $this->db->where('post_up_user', $userId);
        $query = $this->db->get('post_ups');
        return ($query->num_rows() > 0) ? true : false;
    }

    public function hasDown($postId) {
        if ($this->session->userdata('user_id')) {
            $userId = $this->session->userdata('user_id');
        } else {
            $userId = 35;
        }
        $this->db->where('post_down_post', $postId);
        $this->db->where('post_down_user', $userId);
        $query = $this->db->get('post_downs');
        return ($query->num_rows() > 0) ? true : false;
    }

    public function createUp($postId) {
        if ($this->session->userdata('user_id')) {
            $userId = $this->session->userdata('user_id');
        } else {
            $userId = 35;
        }
        $data = array(
            'post_up_user' => $userId,
            'post_up_post' => $postId
        );
        $this->db->insert('post_ups', $data);
    }

    public function createDown($postId) {
        if ($this->session->userdata('user_id')) {
            $userId = $this->session->userdata('user_id');
        } else {
            $userId = 35;
        }
        $data = array(
            'post_down_user' => $userId,
            'post_down_post' => $postId
        );
        $this->db->insert('post_downs', $data);
    }

    public function deleteUp($postId) {
        if ($this->session->userdata('user_id')) {
            $userId = $this->session->userdata('user_id');
        } else {
            $userId = 35;
        }
        $this->db->where('post_up_post', $postId);
        $this->db->where('post_up_user', $userId);
        $this->db->delete('post_ups');
    }

    public function deleteDown($postId) {
        if ($this->session->userdata('user_id')) {
            $userId = $this->session->userdata('user_id');
        } else {
            $userId = 35;
        }
        $this->db->where('post_down_post', $postId);
        $this->db->where('post_down_user', $userId);
        $this->db->delete('post_downs');
    }

    public function addLike($postId) {
        $this->db->where('post_id', $postId);
        $post = $this->db->get('posts');
        $data = array(
            'post_likes' => $post->row()->post_likes + 1
        ); //or do recalculation based on POST_UPS table
        $this->db->where('post_id', $postId);
        $this->db->update('posts', $data);
    }

    public function removeLike($postId) {
        $this->db->where('post_id', $postId);
        $post = $this->db->get('posts');
        $data = array(
            'post_likes' => $post->row()->post_likes - 1
        ); //or do recalculation based on POST_UPS table
        $this->db->where('post_id', $postId);
        $this->db->update('posts', $data);
    }

    public function addDislike($postId) {
        $this->db->where('post_id', $postId);
        $post = $this->db->get('posts');
        $data = array(
            'post_dislikes' => $post->row()->post_dislikes + 1
        ); //or do recalculation based on POST_UPS table
        $this->db->where('post_id', $postId);
        $this->db->update('posts', $data);
    }

    public function removeDislike($postId) {
        $this->db->where('post_id', $postId);
        $post = $this->db->get('posts');
        $data = array(
            'post_dislikes' => $post->row()->post_dislikes - 1
        ); //or do recalculation based on POST_UPS table
        $this->db->where('post_id', $postId);
        $this->db->update('posts', $data);
    }

    public function up($postId) {
//        Events::log_message('debug', "Session : USER : ID : " . $this->session->userdata('user_id'));
        if (!$this->hasUp($postId)) {
            if (!$this->hasDown($postId)) {
                $this->createUp($postId);
                $this->addLike($postId);
                return 'inc';
            } else {
                $this->removeDislike($postId);
                $this->deleteDown($postId);
                return 'other';
            }
        } else {
            $this->removeLike($postId);
            $this->deleteUp($postId);
            return 'dec';
        }
    }

    public function down($postId) {
//        Events::log_message("Session : USER : ID : " . $this->session->userdata('user_id'));
        if (!$this->hasDown($postId)) {
            if (!$this->hasUp($postId)) {
                $this->createDown($postId);
                $this->addDislike($postId);
                return 'inc';
            } else {
                $this->removeLike($postId);
                $this->deleteUp($postId);
                return 'other';
            }
        } else {
            $this->removeDislike($postId);
            $this->deleteDown($postId);
            return 'dec';
        }
    }

    public function getLikesOfUser($user) {
        $this->db->where('post_up_user', $user);
        $query = $this->db->get('post_ups');
        $items = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $item) {
                array_push($items, $item->post_up_post);
            }
        }
        return $items;
    }

    public function getDislikesOfUser($user) {
        $this->db->where('post_down_user', $user);
        $query = $this->db->get('post_downs');
        $items = array();
        if ($query->num_rows() > 0) {
            foreach ($query->result() as $item) {
                array_push($items, $item->post_down_post);
            }
        }
        return $items;
    }

    public function getLikesOfPost($postId) {
        $this->db->select('post_up_id');
        $this->db->from('post_ups');
        $this->db->where('post_up_post', $postId);
        return $this->db->count_all_results();
    }

    public function getDislikesOfPost($postId) {
        $this->db->select('post_down_id');
        $this->db->from('post_downs');
        $this->db->where('post_down_post', $postId);
        return $this->db->count_all_results();
    }

}

?>