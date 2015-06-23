<?php
/**
 * HUBzero CMS
 *
 * Copyright 2005-2015 Purdue University. All rights reserved.
 *
 * This file is part of: The HUBzero(R) Platform for Scientific Collaboration
 *
 * The HUBzero(R) Platform for Scientific Collaboration (HUBzero) is free
 * software: you can redistribute it and/or modify it under the terms of
 * the GNU Lesser General Public License as published by the Free Software
 * Foundation, either version 3 of the License, or (at your option) any
 * later version.
 *
 * HUBzero is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Lesser General Public License for more details.
 *
 * You should have received a copy of the GNU Lesser General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 *
 * HUBzero is a registered trademark of Purdue University.
 *
 * @package   hubzero-cms
 * @author    Christopher Smoak <csmoak@purdue.edu>
 * @copyright Copyright 2005-2015 Purdue University. All rights reserved.
 * @license   http://www.gnu.org/licenses/lgpl-3.0.html LGPLv3
 */

// No direct access
defined('_HZEXEC_') or die();

//set title
Toolbar::title(Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_STATS'), 'stats.png');

//add buttons
Toolbar::custom('cancel', 'back', '', 'COM_NEWSLETTER_TOOLBAR_BACK', false);

// add css & js to view
$this->css();
$this->js()
     ->js('jvectormap/jquery.jvectormap.min.js', 'system')
     ->js('jvectormap/maps/jquery.jvectormap.us.js', 'system')
     ->js('jvectormap/maps/jquery.jvectormap.world.js', 'system');
?>

<?php
	if ($this->getError())
	{
		echo '<p class="error">' . $this->getError() . '</p>';
	}
?>

<form action="<?php echo Route::url('index.php?option=' . $this->option); ?>" method="post" name="adminForm" id="item-form">
	<fieldset class="adminform">
		<legend><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_STATISTICS'); ?></legend>

		<table class="adminlist">
			<tbody>
				<tr>
					<th><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_OPENRATE'); ?>:</th>
					<td>
						<?php
							if (count($this->recipients) > 0)
							{
								echo number_format((count($this->opens) / count($this->recipients)) * 100) . '% ';
								echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_OPENED', count($this->opens), count($this->recipients));
							}
						?>
					</td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_BOUNCERATE'); ?>:</th>
					<td>
						<?php
							if (count($this->recipients) > 0)
							{
								echo (count($this->bounces) / count($this->recipients)) * 100 . '% ';
							}
						?>
					</td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_FORWARDS'); ?>:</th>
					<td>
						<?php echo count($this->forwards); ?>
					</td>
				</tr>
				<tr>
					<th><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_PRINTS'); ?>:</th>
					<td>
						<?php echo count($this->prints); ?>
					</td>
				</tr>
			</tbody>
		</table>
	</fieldset>

	<fieldset class="adminform">
		<legend><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_OPENS_BY_LOCATION'); ?></legend>
		<div class="col width-30">
			<table class="adminlist">
				<thead>
					<tr>
						<th colspan="2"><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_TOP_LOCATIONS'); ?></th>
						<th><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_TOP_LOCATIONS_OPENS_COUNT'); ?></th>
					</tr>
				</thead>
				<tbody>
					<?php foreach ($this->opensGeo['country'] as $country => $count) : ?>
						<tr>
							<td>
								<?php if ($country != 'undetermined') : ?>
									<img src="<?php echo Request::base(); ?>/core/assets/images/flags/<?php echo strtolower($country); ?>.gif" alt="<?php echo $country; ?>" />
								<?php endif; ?>
							</td>
							<td><?php echo strtoupper($country); ?></td>
							<td><?php echo $count; ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
		</div>
		<div class="col width-70 fltrt">
			<?php
				//removed undertermined as we cant put that on the map
				//json encode so we can get value with js
				unset($this->opensGeo['country']['undetermined']);
				unset($this->opensGeo['state']['undetermined']);
				$countryGeo = strtoupper(json_encode( $this->opensGeo['country'] ));
				$stateGeo = strtoupper(json_encode( $this->opensGeo['state'] ));
			?>
			<div id="world-map-data" data-src='<?php echo $countryGeo; ?>'></div>
			<div id="us-map-data" data-src='<?php echo $stateGeo; ?>'></div>
			<div id="location-map-container">
				<div id="us-map"></div>
				<div id="world-map"></div>
				<div class="jvectormap-world"><?php echo Lang::txt('COM_NEWSLETTER_WORLD_MAP'); ?></div>
			</div>
		</div>
	</fieldset>

	<fieldset class="adminform">
		<legend><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_CLICK_THROUGHS'); ?></legend>

		<table class="adminlist">
			<thead>
				<tr>
					<th scope="col"><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_CLICK_THROUGHS_URL'); ?></th>
					<th scope="col"><?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_CLICK_THROUGHS_COUNT'); ?></th>
				</tr>
			</thead>
			<tbody>
				<?php if (count($this->clicks) > 0) : ?>
					<?php foreach ($this->clicks as $url => $count) : ?>
						<tr>
							<td><?php echo '<a target="_blank" href="' . $url . '">' . $url . '</a>'; ?></td>
							<td><?php echo number_format($count); ?></td>
						</tr>
					<?php endforeach; ?>
				<?php else : ?>
					<tr>
						<td colspan="2">
							<?php echo Lang::txt('COM_NEWSLETTER_NEWSLETTER_MAILING_NO_CLICK_THROUGHS'); ?>
						</td>
					</tr>
				<?php endif; ?>
			</tbody>
		</table>
	</fieldset>

	<input type="hidden" name="option" value="<?php echo $this->option; ?>" />
	<input type="hidden" name="controller" value="<?php echo $this->controller; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />

	<?php echo Html::input('token'); ?>
</form>