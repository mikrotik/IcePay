<?php echo form_open_multipart(admin_url('projects/upload_file/'.$project->id),array('class'=>'dropzone','id'=>'project-files-upload')); ?>
<input type="file" name="file" multiple />
<?php echo form_close(); ?>
<small class="mtop5"><?php echo _l('project_file_visible_to_customer'); ?></small><br />
<div class="onoffswitch">
    <input type="checkbox" name="visible_to_customer" id="pf_visible_to_customer" class="onoffswitch-checkbox">
    <label class="onoffswitch-label" for="pf_visible_to_customer"></label>
</div>
<div class="text-right pull-right">
    <div id="dropbox-chooser" style="margin-top:-25px;"></div>
</div>
<div class="clearfix mtop25"></div>
<div class="table-responsive">
    <table class="table dt-table" data-order-col="6" data-order-type="desc">
        <thead>
            <tr>
                <th><?php echo _l('project_file_filename'); ?></th>
                <th><?php echo _l('project_file__filetype'); ?></th>
                <th><?php echo _l('project_discussion_last_activity'); ?></th>
                <th><?php echo _l('project_discussion_total_comments'); ?></th>
                <th><?php echo _l('project_file_visible_to_customer'); ?></th>
                <th><?php echo _l('project_file_uploaded_by'); ?></th>
                <th><?php echo _l('project_file_dateadded'); ?></th>
                <th><?php echo _l('options'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach($files as $file){ ?>
            <tr>
                <td data-order="<?php echo $file['file_name']; ?>">
                    <a href="#" onclick="view_project_file(<?php echo $file['id']; ?>,<?php echo $file['project_id']; ?>); return false;">
                        <?php if(is_image(PROJECT_ATTACHMENTS_FOLDER .$project->id.'/'.$file['file_name']) || (!empty($file['external']) && !empty($file['thumbnail_link']))){
                          $url = base_url('uploads/projects/'.$project->id.'/'.$file['file_name']);
                          if(!empty($file['external']) && !empty($file['thumbnail_link'])){
                            $url = $file['thumbnail_link'];
                        }
                        echo '<img class="project-file-image" src="'.$url.'" width="100">';
                    }
                    echo $file['subject']; ?></a>
                </td>
                <td data-order="<?php echo $file['filetype']; ?>"><?php echo $file['filetype']; ?></td>
                <td data-order="<?php echo $file['last_activity']; ?>">
                    <?php
                    if(!is_null($file['last_activity'])){
                        echo time_ago($file['last_activity']);
                    } else {
                        echo _l('project_discussion_no_activity');
                    }
                    ?>
                </td>
                <?php $total_file_comments = total_rows('tblprojectdiscussioncomments',array('discussion_id'=>$file['id'],'discussion_type'=>'file')); ?>
                <td data-order="<?php echo $total_file_comments; ?>">
                    <?php echo $total_file_comments; ?>
                </td>
                <td data-order="<?php echo $file['visible_to_customer']; ?>">
                    <?php
                    $checked = '';
                    if($file['visible_to_customer'] == 1){
                        $checked = 'checked';
                    }
                    ?>
                    <div class="onoffswitch">
                        <input type="checkbox" data-switch-url="<?php echo admin_url(); ?>projects/change_file_visibility" id="<?php echo $file['id']; ?>" data-id="<?php echo $file['id']; ?>" class="onoffswitch-checkbox" value="<?php echo $file['id']; ?>" <?php echo $checked; ?>>
                        <label class="onoffswitch-label" for="<?php echo $file['id']; ?>"></label>
                    </div>

                </td>
                <td>
                    <?php if($file['staffid'] != 0){
                        $_data = '<a href="' . admin_url('staff/profile/' . $file['staffid']). '">' .staff_profile_image($file['staffid'], array(
                            'staff-profile-image-small'
                            )) . '</a>';
                        $_data .= ' <a href="' . admin_url('staff/member/' . $file['staffid'])  . '">' . get_staff_full_name($file['staffid']) . '</a>';
                        echo $_data;
                    } else {
                     echo ' <img src="'.contact_profile_image_url($file['contact_id'],'thumb').'" class="client-profile-image-small mrigh5">
                     <a href="'.admin_url('clients/client/'.get_user_id_by_contact_id($file['contact_id']).'?contactid='.$file['contact_id']).'">'.get_contact_full_name($file['contact_id']).'</a>';
                 }
                 ?>
             </td>
             <td data-order="<?php echo $file['dateadded']; ?>"><?php echo _dt($file['dateadded']); ?></td>
             <td>
                 <?php if(empty($file['external'])){ ?>
                 <button type="button" data-toggle="modal" data-original-file-name="<?php echo $file['file_name']; ?>" data-filetype="<?php echo $file['filetype']; ?>" data-path="<?php echo PROJECT_ATTACHMENTS_FOLDER .$project->id.'/'.$file['file_name']; ?>" data-target="#send_file" class="btn btn-info btn-icon"><i class="fa fa-envelope"></i></button>
                 <?php } ?>
                 <?php if($file['staffid'] == get_staff_user_id() || has_permission('projects','','delete')){ ?>
                 <a href="<?php echo admin_url('projects/remove_file/'.$project->id.'/'.$file['id']); ?>" class="btn btn-danger btn-icon _delete"><i class="fa fa-remove"></i></a>
                 <?php } ?>
             </td>
         </tr>
         <?php } ?>
     </tbody>
 </table>

</div>
<div id="project_file_data"></div>
<?php include_once(APPPATH . 'views/admin/clients/modals/send_file_modal.php'); ?>
