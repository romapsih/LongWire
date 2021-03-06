<?php

if (!defined('BASEPATH'))
    exit('No direct script access allowed');

class Post_model extends CI_Model {
    /*
     * POST
     * post_id
     * post_user_id
     * post_date
     * post_name
     * post_desc
     * post_body
     * post_likes
     * post_dislikes
     * post_fav
     * post_tags
     */

    public function __construct() {
        parent::__construct();
        $this->load->model('tag_model');
    }

    public function getLastPostId($userId) {
        $query = $this->db->query('SELECT post_id FROM posts WHERE post_user_id = ' . $userId . ' ORDER BY post_date DESC LIMIT 1');
        if ($query->num_rows() > 0) {
            return $query->row()->post_id;
        }
        return false;
    }

    public function addPost() {
        if ($this->session->userdata('user_id')) {
            $userId = $this->session->userdata('user_id');
        } else {
            $userId = 35;//set anonym
        }
        $date = date("Y-m-d H:i:s");
        $data = array(
            'post_user_id' => $userId,
            'post_date' => $date,
            'post_name' => $this->input->post('post_name'),
            'post_desc' => $this->input->post('post_desc'),
            'post_body' => $this->input->post('post_body'),
            'post_tags' => $this->input->post('post_tags')
        );
        $this->db->insert('posts', $data);
        $id = $this->db->insert_id();
        $this->tag_model->addTagsWithPost($this->input->post('post_tags'), $id);
        return $id;
        //return postId //if postId doesn't return - get last post by data
    }

    public function getPost($postId) {
        $this->db->where('post_id', $postId);
        $query = $this->db->get('posts');
        $row = (array) $query->row();
        $row['post_user'] = $this->getUserName($row['post_user_id']);
        return $row;
    }

    public function deletePost($postId) {
        $this->db->where('post_id', $postId);
        $this->db->delete('posts');
    }

    public function getAllPostsFromUser($userId) {
        $this->db->where('post_user_id', $userId);
        $this->db->order_by('post_date','desc');
        $query = $this->db->get('posts');
        if ($query->num_rows() > 0) {
            $posts = array();
            foreach ($query->result() as $rows) {
                $rows = (array) $rows;
                $rows['post_user'] = $this->getUserName($userId);
                array_push($posts, $rows);
            }
            return $posts;
        }
        return false;
    }
    
    public function getAllFavPosts($userId){
        $this->db->where('fav_user', $userId);
        $this->db->from('favorites');
        $this->db->join('posts', 'posts.post_id = favorites.fav_post');
        $query = $this->db->get();
        if($query->num_rows() > 0) {
            $posts = array();            
            foreach ($query->result() as $rows) {
                $rows = (array) $rows;
                $rows['post_user'] = $this->getUserName($rows['post_user_id']);
                array_push($posts, $rows);
            }
            return $posts;
        }
        return false;
    }
    
    public function getAllLikedPosts($userId){
        $this->db->where('post_up_user', $userId);
        $this->db->from('post_ups');
        $this->db->join('posts', 'posts.post_id = post_ups.post_up_post');
        $query = $this->db->get();
        if($query->num_rows() > 0) {
            $posts = array();            
            foreach ($query->result() as $rows) {
                $rows = (array) $rows;
                $rows['post_user'] = $this->getUserName($rows['post_user_id']);
                array_push($posts, $rows);
            }
            return $posts;
        }
        return false;
    }

    public function getUserName($userId) {
        $this->db->where('user_id', $userId);
        $query = $this->db->get('users');
        if ($query->num_rows() > 0) {
            return $query->row()->user_login;
        }
        return 'unknown';
    }

    //Don't use this function in future
    public function getAllPosts($page = 0) {
        $this->db->order_by('post_date','desc');
        $query = $this->db->get('posts',10, 10*$page);
        if ($query->num_rows() > 0) {
            $posts = array();
            foreach ($query->result() as $rows) {
                $rows = (array) $rows;
                $rows['post_user'] = $this->getUserName($rows['post_user_id']);
                array_push($posts, $rows);
            }
            return $posts;
        }
        return false;
    }

    /*
     * $order - can be by time, popularity, top rated, most favoutites
     *  - 'time'
     *  - 'pop'
     *  - 'top'
     *  - 'fav'
     * $limit - count of posts on page
     */

    public function getAllPostsLimit($order, $limit) {
        
    }
    
    public function getBestPosts(){
        $this->db->order_by('post_likes','desc');
        $this->db->limit(25);
        $query = $this->db->get('posts');
        if ($query->num_rows() > 0) {
            $posts = array();
            foreach ($query->result() as $rows) {
                $rows = (array) $rows;
                $rows['post_user'] = $this->getUserName($rows['post_user_id']);
                array_push($posts, $rows);
            }
            return $posts;
        }
        return false;
    }

    public function updatePost() {
        $data = array(
            'post_name' => $this->input->post('post_name'),
            'post_desc' => $this->input->post('post_desc'),
            'post_body' => $this->input->post('post_body'),
            'post_tags' => $this->input->post('post_tags')
        );
        $postId = $this->input->post('post_id');
        $this->db->where('post_id', $postId);
        $this->db->update('posts', $data);
        $this->tag_model->addTagsWithPost($this->input->post('post_tags'), $postId);
        return $postId;
    }

    public function checkBeforeEdit($postId, $login) {
        $post = (array) $this->getPost($postId);
        if ($this->getUserName($post['post_user_id']) != $login) {
            return false;
        }
        return true;
    }

}

?>