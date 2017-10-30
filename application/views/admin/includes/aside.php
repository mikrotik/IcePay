<aside id="menu" class="sidebar">
 <ul class="nav metis-menu" id="side-menu">
  <li class="dashboard_user">
   <?php echo _l('welcome_top',$_staff->firstname); ?> <i class="fa fa-power-off top-left-logout pull-right" data-toggle="tooltip" data-title="<?php echo _l('nav_logout'); ?>" data-placement="left" onclick="logout(); return false;"></i>
 </li>
 <?php
 $total_qa_removed = 0;
 foreach($_quick_actions as $key => $item){
  if(isset($item['permission'])){
   if(!has_permission($item['permission'],'','create')){
    $total_qa_removed++;
  }
}
}
?>
<?php if($total_qa_removed != count($_quick_actions)){ ?>
<li class="quick-links">
  <div class="dropdown dropdown-quick-links">
    <a href="#" class="dropdown-toggle" id="dropdownQuickLinks" data-toggle="dropdown" aria-haspopup="true" aria-expanded="true">
     <i class="fa fa-gavel" aria-hidden="true"></i>
   </a>
   <ul class="dropdown-menu" aria-labelledby="dropdownQuickLinks">
     <?php
     foreach($_quick_actions as $key => $item){
      $url = '';
      if(isset($item['permission'])){
       if(!has_permission($item['permission'],'','create')){
        continue;
      }
    }
    if(isset($item['custom_url'])){
     $url = $item['url'];
   } else {
     $url = admin_url(''.$item['url']);
   }
   $href_attributes = '';
   if(isset($item['href_attributes'])){
     foreach ($item['href_attributes'] as $key => $val) {
      $href_attributes .= $key . '=' . '"' . $val . '"';
    }
  }
  ?>
  <li>
    <a href="<?php echo $url; ?>" <?php echo $href_attributes; ?>><?php echo $item['name']; ?></a>
  </li>
  <?php } ?>
</ul>
</div>

</li>
<?php } ?>
<?php
do_action('before_render_aside_menu');
$menu_active = get_option('aside_menu_active');
$menu_active = json_decode($menu_active);
$m = 0;
foreach($menu_active->aside_menu_active as $item){
 if($item->id == 'tickets'){
  if(get_option('access_tickets_to_none_staff_members') == 0 && !is_staff_member()){
   continue;
 }
} else if($item->id == 'customers'){
 if(!has_permission('customers','','view')){
  if(have_assigned_customers() || (!have_assigned_customers() && has_permission('customers','','create'))){
     $item->permission = '';
   }
 }
}
if(isset($item->permission) && !empty($item->permission)){
 if(!has_permission($item->permission,'','view') && !has_permission($item->permission,'','view_own')){
   continue;
 }
}
$submenu = false;
$remove_main_menu = false;
$url = '';
if(isset($item->children)){
  $submenu = true;
  $total_sub_items_removed = 0;
  foreach($item->children as $_sub_menu_check){
   if(isset($_sub_menu_check->permission) && !empty($_sub_menu_check->permission) && $_sub_menu_check->permission != 'payments'){
    if(!has_permission($_sub_menu_check->permission,'','view') && !has_permission($_sub_menu_check->permission, '', 'view_own')){
     $total_sub_items_removed++;
   }
 } else if($_sub_menu_check->permission == 'payments'){
   if(!has_permission('payments','','view') && !has_permission('invoices','','view_own')){
     $total_sub_items_removed++;
   }
 }
}
if($total_sub_items_removed == count($item->children)){
  $submenu = false;
  $remove_main_menu = true;
}
} else {
  if($item->url == '#'){continue;}
  $url = $item->url;
}
if($remove_main_menu == true){
  continue;
}
$url = $item->url;
if(!_startsWith($url,'http://') && $url != '#'){
 $url = admin_url($url);
}
?>
<li class="menu-item-<?php echo $item->id; ?>">
 <a href="<?php echo $url; ?>" aria-expanded="false"><i class="<?php echo $item->icon; ?> menu-icon"></i>
   <?php echo _l($item->name); ?>
   <?php if($submenu == true){ ?>
   <span class="fa arrow"></span>
   <?php } ?>
 </a>
 <?php if(isset($item->children)){ ?>
 <ul class="nav nav-second-level collapse" aria-expanded="false">
  <?php foreach($item->children as $submenu){
   if(isset($submenu->permission) && !empty($submenu->permission) && $submenu->permission != 'payments'){
    if(!has_permission($submenu->permission,'','view') && !has_permission($submenu->permission, '', 'view_own')){
      continue;
    }
  } else if($submenu->permission == 'payments'){
   if(!has_permission('payments','','view') && !has_permission('invoices','','view_own')){
     continue;
   }
 }
 $url = $submenu->url;
 if(!_startsWith($url,'http://')){
   $url = admin_url($url);
 }
 ?>
 <li class="sub-menu-item-<?php echo $submenu->id; ?>"><a href="<?php echo $url; ?>">
   <?php if(!empty($submenu->icon)){ ?>
   <i class="<?php echo $submenu->icon; ?> menu-icon"></i>
   <?php } ?>
   <?php echo _l($submenu->name); ?></a>
 </li>
 <?php } ?>
</ul>
<?php } ?>
</li>
<?php
$m++;
do_action('after_render_single_aside_menu',$m); ?>
<?php } ?>
<?php do_action('after_render_aside_menu'); ?>
<?php if((is_staff_member() || is_admin()) && $this->perfex_base->show_setup_menu() == true){ ?>
<li<?php if(get_option('show_setup_menu_item_only_on_hover') == 1) { echo ' style="display:none;"'; } ?> id="setup-menu-item">
<a href="#" class="open-customizer"><i class="fa fa-cog menu-icon"></i>
 <?php echo _l('setting_bar_heading'); ?></a>
 <?php } ?>
</li>
<?php if(count($_pinned_projects) > 0){ ?>
<li class="pinned-separator"></li>
<?php foreach($_pinned_projects as $_pinned_project){ ?>
<li class="pinned_project">
 <a href="<?php echo admin_url('projects/view/'.$_pinned_project['id']); ?>" data-toggle="tooltip" data-title="<?php echo _l('pinned_project'); ?>"><?php echo $_pinned_project['name']; ?></a>
 <div class="col-md-12">
  <div class="progress progress-bar-mini">
   <div class="progress-bar no-percent-text not-dynamic" role="progressbar" data-percent="<?php echo $_pinned_project['progress']; ?>" style="width: <?php echo $_pinned_project['progress']; ?>%;">
   </div>
 </div>
</div>
</li>
<?php } ?>
<?php } ?>
</ul>
</aside>
