<script type="text/javascript">

    /*
         Initialize and render the Menu when its elements are ready 
         to be scripted.
    */
    
    YAHOO.util.Event.onContentReady("containermenu", function () {
    
        /*
             Instantiate a Menu:  The first argument passed to the 
             constructor is the id of the element in the page 
             representing the Menu; the second is an object literal 
             of configuration properties.
        */

        var oMenu = new YAHOO.widget.Menu("containermenu", { 
                                                position: "static", 
                                                hidedelay:  750, 
                                                lazyload: true });
    
        /*
             Call the "render" method with no arguments since the 
             markup for this Menu instance is already exists in the page.
        */
    
        oMenu.render();            
    
    });

</script>
<div id="containermenu" class="yuimenu">
        <div class="bd">
        <ul class="first-of-type">
        	<?foreach ($entities as $entity){
				if ($entity->getHooked()!=""){?>
					<li class="yuimenuitem first-of-type"><a class="yuimenuitemlabel" href="javascript:loadHook('<?=$entity->getHooked()?>','','<?=$entity->getTitle()?>','<?=$entity->getColor()?>')"><?=$entity->getTitle()?></a>
				<?}else{
					if ($entity->getMaintanceType()>0){
						if ($entity->getMaintanceType()==1){
							if ($entity->getByUser()){?>
								<li class="yuimenuitem first-of-type"><a class="yuimenuitemlabel" href="javascript:consultar('<?=$entity->getIdEntity()?>','<?=$_SESSION[constant(USER.PROJECT)]?>','','<?=$entity->getTitle()?>','<?=$entity->getColor()?>')"><?=$entity->getTitle()?></a>
							<?}else{?>
								<li class="yuimenuitem first-of-type"><a class="yuimenuitemlabel" href="javascript:consultar('<?=$entity->getIdEntity()?>','1','','<?=$entity->getTitle()?>','<?=$entity->getColor()?>')"><?=$entity->getTitle()?></a>
						<?}}else{?>
							<li class="yuimenuitem first-of-type"><a class="yuimenuitemlabel" href="javascript:listar('<?=$entity->getIdEntity()?>','','<?=$entity->getTitle()?>','<?=$entity->getColor()?>')"><?=$entity->getTitle()?></a>
	            <?}}}
	          }?>
			</li>
        </ul>            
    </div>
</div>
                
                
                <!-- submenus
                <li class="yuimenuitem first-of-type"><a class="yuimenuitemlabel" href="#communication">Communication</a>
                	<div id="communication" class="yuimenu">
                    <div class="bd">
                        <ul>

                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="http://360.yahoo.com">360&#176;</a></li>
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="http://alerts.yahoo.com">Alerts</a></li>
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="http://avatars.yahoo.com">Avatars</a></li>
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="http://groups.yahoo.com">Groups</a></li>
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="http://promo.yahoo.com/broadband/">Internet Access</a></li>
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="#">PIM</a>

                            
                                <div id="pim" class="yuimenu">
                                    <div class="bd">
                                        <ul class="first-of-type">
                                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="http://mail.yahoo.com">Yahoo! Mail</a></li>
                                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="http://addressbook.yahoo.com">Yahoo! Address Book</a></li>
                                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="http://calendar.yahoo.com">Yahoo! Calendar</a></li>
                                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="http://notepad.yahoo.com">Yahoo! Notepad</a></li>

                                        </ul>            
                                    </div>
                                </div>                    
                            
                            </li>
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="http://members.yahoo.com">Member Directory</a></li>
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="http://messenger.yahoo.com">Messenger</a></li>
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="http://mobile.yahoo.com">Mobile</a></li>
                            <li class="yuimenuitem"><a class="yuimenuitemlabel" href="http://www.flickr.com">Flickr Photo Sharing</a></li>

                        </ul>
                    </div>
                </div>-->
            
