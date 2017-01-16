<?php if(!class_exists('raintpl')){exit;}?><div class="gc-conf">
	<div class="gc-conf-head-wrapper left"> <?php echo $conf_head;?> </div>
	<div class="gc-conf-img right">
		<img src="<?php echo $conf_img;?>" alt="configuration image">
		<div class="conf-share-wrapper"><div class="share-black"></div></div>
	</div>
</div>

<div class="gc-conf-name gc_head_name">
	<p class="gc-headconf-name conf_name"> <?php echo $conf_name;?> </p>
	<p class="gc-head-saved gc_head_saved">Saved on <span class="gc_saved_on"> <?php echo $conf_saved_date;?> </span> </p>
</div>

<div class="gc-conf-detail">
	<div class="gc-standard left"> <?php $tpl = new RainTPL;$tpl_dir_temp = self::$tpl_dir;$tpl->assign( $this->var );$tpl->draw( dirname("mainpart_descr") . ( substr("mainpart_descr",-1,1) != "/" ? "/" : "" ) . basename("mainpart_descr") );?> </div>
	<div class="gc-added right">
		<p class="gc-headconf-name">You Added:</p>
		<div class="external_parts_descr">
			<?php echo $external_parts_descr;?>

		</div>
	</div>
</div>