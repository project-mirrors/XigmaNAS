			</div>
		</main>
		<footer id="g4f">
			<div id="gapfooter"></div>
			<div id="pagefooter" class="lcrgridx">
				<div class="g4fl lcrgridl">
<?php
					if(Session::isAdmin() && file_exists($d_sysrebootreqd_path)):
?>
						<a href="/reboot.php" class="g4fi">
							<img src="/images/notify_reboot.png" title="<?=gtext('A reboot is required');?>" alt="<?=gtext('Reboot Required');?>">
						</a>
<?php
					endif;
?>
				</div>
				<div class="g4fc lcrgridc"><?=htmlspecialchars(get_product_copyright());?></div>
				<div class="g4fr lcrgridr"><?=htmlspecialchars(system_get_hostname());?></div>
			</div>
		</footer>
	</body>
</html>
