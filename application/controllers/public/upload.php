<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Upload extends CI_Controller {

    // public function __construct()
    // {
    // 	$this->load->model('user_model');
    // }

    private function check_token($key)
    {
        $token = md5($this->config->item('encryption_key').$key);
        return $token == $_POST['token'] ? true : false;
    }

    public function uploadIntegralimg()
    {
        $type = $this->input->post['type'];
        $uid = 'admin';
        if(!$type){
            $type = 'integr';
        }
        $uploadName = $type."_upload";
        if (isset($_FILES['Filedata']) && $_FILES['Filedata']['name'])
        {
            $config = array();
            $config['upload_path'] = "upload/$type/".date('Ym');
            $config['allowed_types']= '*';	//jpg|png|jpeg
            $config['max_size']     = 1500;
            $config['overwrite']    = true;
            $config['file_name']    = $type.'_'.$uid.'_'.time();
            mkpath($config['upload_path']);
            $this->load->library('upload', $config);
            if ( ! $this->upload->do_upload('Filedata')){
                $error = $this->upload->display_errors();
                output_error(-1,'上传失败');
            }
            else{
                $upload_data = $this->upload->data();
                // $this->load->model('Attach_model');
                // $map = array(
                //   'attach_type'=>$this->router->fetch_method(),
                //   'uid'=>$uid,
                //   'ctime'=>$this->timestamp,
                //   'name'=>$upload_data['client_name'],
                //   'type'=>$upload_data['file_type'],
                //   'size'=>$upload_data['file_size'],
                //   'extension'=>$upload_data['file_ext'],
                //   'save_path'=>$config['upload_path'],
                //   'save_name'=>$upload_data['orig_name'],
                //   'ip'=>$this->input->ip_address(),
                // );
                // $aid = $this->Attach_model->insert($map);
                $pic  = $config['upload_path'].'/'.$upload_data['file_name'];
            }
            if ($pic){
                output_data(array('pic_url' =>$pic,'url' => BASE_SITE_URL.'/'.$pic));
            }
        }else{
            output_error(-1,'上传失败');
        }
    }

    /**
     *上传图片
     * $type: 控件id
     */
    public function uploadimg()
    {
        $timestamp = $_POST['timestamp'];
        $uid = (int)$_POST['uid'];
        $type = $_POST['type'];
        $uploadName = $type."_upload";
        if (isset($_FILES['Filedata']) && $_FILES['Filedata']['name'] && $this->check_token($timestamp))
        {
            $config = array();
            $config['upload_path'] = "upload/$type/".date('Ym');
            $config['allowed_types']= '*';	//jpg|png|jpeg
            $config['max_size']     = 1500;
            $config['overwrite']    = true;
            $config['file_name']    = $type.'_'.$uid.'_'.time();
            mkpath($config['upload_path']);
            $this->load->library('upload', $config);
            if ( ! $this->upload->do_upload('Filedata')){
                $error = $this->upload->display_errors();
                echo '100|'.strip_tags($error);exit;
            }
            else{
                $upload_data = $this->upload->data();
                // $this->load->model('Attach_model');
                // $map = array(
                //   'attach_type'=>$this->router->fetch_method(),
                //   'uid'=>$uid,
                //   'ctime'=>$this->timestamp,
                //   'name'=>$upload_data['client_name'],
                //   'type'=>$upload_data['file_type'],
                //   'size'=>$upload_data['file_size'],
                //   'extension'=>$upload_data['file_ext'],
                //   'save_path'=>$config['upload_path'],
                //   'save_name'=>$upload_data['orig_name'],
                //   'ip'=>$this->input->ip_address(),
                // );
                // $aid = $this->Attach_model->insert($map);
                $pic  = $config['upload_path'].'/'.$upload_data['file_name'];
            }
            if ($pic){
                echo '200|'.$pic;exit;
            }
        }
    }

    public function uploadvideo()
    {
        $timestamp = $_POST['timestamp'];
        $uid = (int)$_POST['uid'];
        $type = $_POST['type'];
        $uploadName = $type."_upload";
        if (isset($_FILES['Filedata']) && $_FILES['Filedata']['name'] && $this->check_token($timestamp))
        {
            $config = array();
            $config['upload_path'] = "upload/$type/".date('Ym');
            $config['allowed_types']= '*';  //flv
            $config['max_size']     = 1500;
            $config['overwrite']    = true;
            $config['file_name']    = $type.'_'.$uid.'_'.time();
            mkpath($config['upload_path']);
            $this->load->library('upload', $config);
            if ( ! $this->upload->do_upload('Filedata')){
                $error = $this->upload->display_errors();
                echo '100|'.strip_tags($error);exit;
            }
            else{
                $upload_data = $this->upload->data();
                $pic  = $config['upload_path'].'/'.$upload_data['file_name'];
            }
            if ($pic){
                echo '200|'.$pic;exit;
            }
        }
    }

    public function uploadphotoimg(){
        $timestamp = $_POST['timestamp'];
        $uid = _get_key_val( $_POST['uid'] ,true);
        if(!$uid)
            $uid = (int)$_POST['uid'];
        $type = $_POST['type'];
        $albumid = isset($_POST['albumid'])?_get_key_val($_POST['albumid'],true):0;
        $uploadName = $type."_upload";
        if(!$albumid)
        {
            echo '100|'.strip_tags('请选择相册');exit;
        }
        if (isset($_FILES['Filedata']) && $_FILES['Filedata']['name'] && $this->check_token($timestamp))
        {
            $config = array();
            $config['upload_path'] = "upload/$type/".date('Ym');
            $config['allowed_types']= '*';	//jpg|png|jpeg
            $config['max_size']     = 1500;
            $config['overwrite']    = true;
            $config['file_name']    = $type.'_'.$uid.'_'.time();
            mkpath($config['upload_path']);
            $this->load->library('upload', $config);
            if ( ! $this->upload->do_upload('Filedata')){
                $error = $this->upload->display_errors();
                echo '100|'.strip_tags($error);exit;
            }
            else{
                $upload_data = $this->upload->data();
                $imgurl = '/'.$config['upload_path'].'/'.$upload_data['file_name'];

                $map = array(
                    'albumid'=>$albumid,
                    'userid'=>$uid,
                    'insid'=>$this->loginInsID,
                    'addtime'=>$this->timestamp,
                    'title'=>$upload_data['client_name'],
                    'size'=>$upload_data['file_size'],
                    'ext'=>$upload_data['file_ext'],
                    'img'=>$imgurl,
                    'status'=>1,
                    'ip'=>$this->input->ip_address(),
                );
                $this->load->model('Photo_model');
                $aid = $this->Photo_model->insert($map);
                $this->load->service('Num_service');
                $this->num_service->set_album_photo_num($this->loginID, 'photonum', $albumid);
                $this->load->model('Album_model');
                $this->Album_model->update_by_where(array('id'=>$albumid),array('showimg'=>$imgurl));
                $pic  = $imgurl;
                $name = $map['title'];
            }
            if ($pic){
                echo '200|'.$pic.'|'.$name;exit;
            }
        }
    }
}