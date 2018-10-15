<?php
defined('BASEPATH') OR exit('No direct script access allowed');


class Upload extends TokenApiController {

	public function img(){
		$token = $this->input->post('token');
		$types = $this->input->post('type');
		$user_id = $this->loginUser['user_id'];
        $type = 'img';

        if($types==1){
            $type = 'portrait';
        }elseif($types==2){
            $type = 'card';
        }
		$result = array('code'=>'EMPTY','message'=>'','data'=>'');
        if (isset($_FILES['filedata']) && $_FILES['filedata']['name'])
        {
            $config = array();
            $config['upload_path'] = "upload/$type/".date('Ym');

            $config['allowed_types']= '*';	//jpg|png|jpeg
            $config['max_size']     = 3500;
            $config['overwrite']    = true;
            $config['file_name']    = $type.'_'.$user_id.'_'.time();

            //output_error(-3, $config['upload_path']);

            mkpath($config['upload_path']);
            $this->load->library('upload', $config);
            if ( ! $this->upload->do_upload('filedata')){
                
                $result['code'] = 'Failure';
               	$result['message'] = $this->upload->display_errors();
            }
            else{
                $upload_data = $this->upload->data();
                $path = $config['upload_path'].'/'.$upload_data['file_name'];
                $url = BASE_SITE_URL.'/'.$path;

                $result['data']  = array('path'=>$path, 'url'=>$url);
                $result['code'] = 'Success';
               	$result['message'] = $this->upload->display_errors();
            }
	        //echo json_encode($result);
        }

        if(!empty($result['data'])){
            output_data($result['data']);
        }else{
            output_error(-1, '上传失败');
        }
	}
}
