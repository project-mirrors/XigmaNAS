			</div>
		</main>
		<footer id="g4f">
			<div id="gapfooter"></div>
			<div id="pagefooter">
				<table class="area_data_settings">
					<colgroup>
						<col style="width:20%">
						<col style="width:60%">
						<col style="width:20%">
					</colgroup>
					<tbody>
						<tr>
							<td class="g4fl">
<?php
								if(Session::isAdmin() && file_exists($d_sysrebootreqd_path)):
?>
									<a href="/reboot.php" class="g4fi">
										<img src="/images/notify_reboot.png" title="<?=gtext('A reboot is required');?>" alt="<?=gtext('Reboot Required');?>">
									</a>
<?php
								endif;
?>
							</td>
							<td class="g4fc"><?=htmlspecialchars(get_product_copyright());?></td>
							<td class="g4fr"><?=htmlspecialchars(system_get_hostname());?></td>
						</tr>
					</tbody>
				</table>
			</div>
		</footer>
	</body>
</html>
