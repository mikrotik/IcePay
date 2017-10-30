                      <?php $unread_notifications = total_rows('tblnotifications',array('touserid'=>get_staff_user_id(),'isread'=>0)); ?>
                      <a href="#" class="dropdown-toggle notifications-icon" data-toggle="dropdown" aria-expanded="false">
                      <i class="fa fa-bell"></i>
                        <?php
                        if($unread_notifications > 0){ ?>
                        <span class="label label-warning icon-total-indicator icon-notifications"><?php echo $unread_notifications; ?></span>
                        <?php } ?>
                      </a>
                      <ul class="dropdown-menu notifications animated fadeIn width300">
                        <?php
                        foreach($_notifications as $notification){ ?>
                        <li>
                          <?php if(!empty($notification['link'])){ ?>
                          <a href="<?php echo admin_url($notification['link']); ?>">
                            <?php } ?>
                            <div class="notification-box<?php if($notification['isread'] == 0){echo ' unread';} ?>">
                              <?php
                              if(($notification['fromcompany'] == NULL && $notification['fromuserid'] != 0) || ($notification['fromcompany'] == NULL && $notification['fromclientid'] != 0)){
                                if($notification['fromuserid'] != 0){
                                 echo staff_profile_image($notification['fromuserid'],array('staff-profile-image-small','img-circle','pull-left'));
                               } else {
                                echo '<img src="'.contact_profile_image_url($notification['fromclientid']).'" class="client-profile-image-small img-circle pull-left">';
                              }
                            }
                            ?>
                            <div class="media-body">
                              <?php
                              $additional_data = '';
                              if(!empty($notification['additional_data'])){
                                $additional_data = unserialize($notification['additional_data']);

                                $i = 0;
                                foreach($additional_data as $data){
                                  if(strpos($data,'<lang>') !== false){
                                    $lang = get_string_between($data, '<lang>', '</lang>');
                                    $temp = _l($lang);
                                    if(strpos($temp,'project_status_') !== FALSE){
                                      $temp = project_status_by_id(strafter($temp,'project_status_'));
                                    }
                                    $additional_data[$i] = $temp;
                                  }
                                  $i++;
                                }
                              }
                              $description = _l($notification['description'],$additional_data);
                              if(($notification['fromcompany'] == NULL && $notification['fromuserid'] != 0) || ($notification['fromcompany'] == NULL && $notification['fromclientid'] != 0)){
                               if($notification['fromuserid'] != 0){
                                $description = $notification['from_fullname']. ' - ' . $description;
                              } else {
                                $description = $notification['from_fullname']. ' - ' . $description . '<br /><span class="label inline-block mtop5 label-info">'._l('is_customer_indicator').'</span>';
                              }
                            }
                            echo $description; ?><br />
                            <small class="text-muted"><?php echo time_ago($notification['date']); ?></small>
                          </div>
                        </div>
                        <?php if(!empty($notification['link'])){ ?>
                      </a>
                      <?php } ?>
                    </li>
                    <?php } ?>
                    <?php if(count($_notifications) != 0){ ?>
                    <li class="divider no-mbot"></li>
                    <?php } ?>
                    <li class="text-center">
                      <?php if(count($_notifications) > 0){ ?>
                      <a href="<?php echo admin_url('profile'); ?>"><?php echo _l('nav_view_all_notifications'); ?></a>
                      <?php } else { ?>
                      <?php echo _l('nav_no_notifications'); ?>
                      <?php } ?>
                    </li>
                  </ul>
