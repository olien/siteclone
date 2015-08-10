<?php

$page = rex_request('page', 'string');
$subpage = rex_request('subpage', 'string');
$func = rex_request('func', 'string');

// save settings
if ($func == 'update') {
	$settings = (array) rex_post('settings', 'array', array());

	rex_siteclone_utils::replaceSettings($settings);
	rex_siteclone_utils::updateSettingsFile();
}
?>

<div class="rex-addon-output">
	<div class="rex-form">

		<h2 class="rex-hl2"><?php echo $I18N->msg('website_manager_siteclone_settings'); ?></h2>

		<form action="index.php" method="post">

			<fieldset class="rex-form-col-1">
				<div class="rex-form-wrapper">
					<input type="hidden" name="page" value="<?php echo $page; ?>" />
					<input type="hidden" name="subpage" value="<?php echo $subpage; ?>" />
					<input type="hidden" name="func" value="update" />

					<div class="rex-form-row rex-form-element-v1">
						<p class="rex-form-checkbox">
							<label for="clone_structure"><?php echo $I18N->msg('website_manager_siteclone_settings_clone_structure'); ?></label>
							<input type="hidden" name="settings[clone_structure]" value="0" />
							<input type="checkbox" name="settings[clone_structure]" id="clone_structure" value="1" <?php if ($REX['ADDON']['siteclone']['settings']['clone_structure']) { echo 'checked="checked"'; } ?>>
						</p>
					</div>

					<div class="rex-form-row rex-form-element-v1">
						<p class="rex-form-checkbox">
							<label for="clone_slices"><?php echo $I18N->msg('website_manager_siteclone_settings_clone_slices'); ?></label>
							<input type="hidden" name="settings[clone_slices]" value="0" />
							<input type="checkbox" name="settings[clone_slices]" id="clone_slices" value="1" <?php if ($REX['ADDON']['siteclone']['settings']['clone_slices']) { echo 'checked="checked"'; } ?>>
						</p>
					</div>

					<div class="rex-form-row rex-form-element-v1">
						<p class="rex-form-checkbox">
							<label for="clone_media"><?php echo $I18N->msg('website_manager_siteclone_settings_clone_media'); ?></label>
							<input type="hidden" name="settings[clone_media]" value="0" />
							<input type="checkbox" name="settings[clone_media]" id="clone_media" value="1" <?php if ($REX['ADDON']['siteclone']['settings']['clone_media']) { echo 'checked="checked"'; } ?>>
						</p>
					</div>

					<div class="rex-form-row rex-form-element-v1">
						<p class="rex-form-checkbox">
							<label for="clone_strings"><?php echo $I18N->msg('website_manager_siteclone_settings_clone_strings'); ?></label>
							<input type="hidden" name="settings[clone_strings]" value="0" />
							<input type="checkbox" name="settings[clone_strings]" id="clone_strings" value="1" <?php if ($REX['ADDON']['siteclone']['settings']['clone_strings']) { echo 'checked="checked"'; } ?>>
						</p>
					</div>

					<div class="rex-form-row rex-form-element-v1">
						<p class="rex-form-checkbox">
							<label for="empty_strings"><?php echo $I18N->msg('website_manager_siteclone_settings_empty_strings'); ?></label>
							<input type="hidden" name="settings[empty_strings]" value="0" />
							<input type="checkbox" name="settings[empty_strings]" id="empty_strings" value="1" <?php if ($REX['ADDON']['siteclone']['settings']['empty_strings']) { echo 'checked="checked"'; } ?>>
						</p>
					</div>

					<div class="rex-form-row rex-form-element-v1">
						<p class="rex-form-text">
							<label for="copy_file"><?php echo $I18N->msg('website_manager_siteclone_settings_copy_file'); ?></label>
							<input type="text" value="<?php echo $REX['ADDON']['siteclone']['settings']['copy_file']; ?>" name="settings[copy_file]" id="copy_file" class="rex-form-text">
						</p>
					</div>

					<div class="rex-form-row rex-form-element-v1">
						<p class="rex-form-text">
							<label for="source_webiste_id"><?php echo $I18N->msg('website_manager_siteclone_settings_source_webiste_id'); ?></label>
							<input type="text" value="<?php echo $REX['ADDON']['siteclone']['settings']['source_webiste_id']; ?>" name="settings[source_webiste_id]" id="copy_file" class="rex-form-text">
						</p>
					</div>

					<div class="rex-form-row rex-form-element-v1">
						<p class="rex-form-submit">
							<input type="submit" class="rex-form-submit" name="sendit" value="<?php echo $I18N->msg('website_manager_settings_save'); ?>" />
						</p>
					</div>
				</div>
			</fieldset>
		</form>
	</div>
</div>
