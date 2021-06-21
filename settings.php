<div class="wrap">
    <h2>Limit Post Titles</h2>
    <h4>By Alessandro Magri</h4>
    <form method="post" action="options.php">
    	<?php
    		 settings_fields('am_title_group');
    		 do_settings_sections('am_limiter');
    		 submit_button();
    	?>
    </form>
</div>
