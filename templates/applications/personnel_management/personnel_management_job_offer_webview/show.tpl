<!-- SUB: GROUP.LVL1 -->
{VAR:group.lvl1}<br />
{VAR:JOB_OFFERS}
<!-- SUB: GROUP.LVL2 -->
{VAR:group.lvl2}
<!-- SUB: JOB_OFFERS -->
<table>
	<!-- SUB: JOB_OFFERS.HEADER -->
	<tr>
		<!-- SUB: JOB_OFFERS.HEADER.NAME -->
		<td>
			T&ouml;&ouml;pakkumine
		</td>
		<!-- END SUB: JOB_OFFERS.HEADER.NAME -->
		<!-- SUB: JOB_OFFERS.HEADER.END -->
		<td>
			T&auml;htaeg
		</td>
		<!-- END SUB: JOB_OFFERS.HEADER.END -->
	</tr>
	<!-- END SUB: JOB_OFFERS.HEADER -->
	<!-- SUB: JOB_OFFER -->
	<tr>
		<!-- SUB: JOB_OFFER.NAME -->
		<td>
			<a href="{VAR:job_offer.href}">{VAR:job_offer.name}</a>
		</td>
		<!-- END SUB: JOB_OFFER.NAME -->
		<!-- SUB: JOB_OFFER.END -->
		<td>
			{VAR:job_offer.end}
		</td>
		<!-- END SUB: JOB_OFFER.END -->
	</tr>
	<!-- END SUB: JOB_OFFER -->
</table>
<!-- END SUB: JOB_OFFERS -->
<!-- END SUB: GROUP.LVL2 -->
<!-- END SUB: GROUP.LVL1 -->
