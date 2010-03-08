<?xml version="1.0" encoding="iso-8859-1"?>

<fo:root xmlns:fo="http://www.w3.org/1999/XSL/Format">
  <fo:layout-master-set>
    <fo:simple-page-master master-name="cover">
      <fo:region-body margin="1in"/>
    </fo:simple-page-master>

	<fo:simple-page-master master-name="content-pages">
      <fo:region-body margin-top="70pt" margin-left="50pt" margin-right="50pt" margin-bottom="70pt"/>
      <fo:region-before 
						display-align="before" 
						extent="1in" 
						region-name="page-header"
						margin="10pt"/>

      <fo:region-after 
						extent="1in" 
						region-name="page-footer"
						margin="10pt"/>

       <fo:region-start region-name="LeftSide" extent="0.7in" padding="6pt" 
                                  display-align="after"
                                  />
            <fo:region-end region-name="RightSide" extent="0.7in" padding="6pt" 
                                  display-align="after"
                                  />
    </fo:simple-page-master>

	<fo:page-sequence-master master-name="offer">
      <fo:single-page-master-reference master-reference="cover"/>
      <fo:repeatable-page-master-reference master-reference="content-pages"/>
    </fo:page-sequence-master>

  </fo:layout-master-set>

  <fo:page-sequence master-reference="offer">
	<fo:static-content flow-name="page-header">
		<fo:block
					text-align-last="justify"
					font-size="12px"
					font-family="Helvetica"
					space-before="20pt"
					space-before.conditionality="retain">
			Pakkumine
			<fo:leader leader-pattern="space"/>
			<fo:inline 
					font-size="9px"
					font-family="Helvetica">
				{VAR:date}
			</fo:inline>
		</fo:block>
		<fo:block
					text-align-last="justify"
					space-before="7pt"
					space-before.conditionality="retain"
					font-size="9px"
					font-family="Helvetica">
			{VAR:orderer}
			<fo:leader leader-pattern="space"/>
			<fo:page-number/> / <fo:page-number-citation ref-id="theEnd"/>
		</fo:block>
		<fo:block
					text-align-last="justify"
					font-size="9px"
					space-before="5pt"
					space-before.conditionality="retain"
					border-bottom-width="0.1pt"
					border-bottom-style="solid"
					border-bottom-color="#dc2300"
					font-family="Helvetica">
		</fo:block>
	</fo:static-content>

	<fo:static-content flow-name="page-footer">
		<fo:block
					text-align-last="justify"
					font-size="9px"
					space-before="5pt"
					space-before.conditionality="retain"
					border-bottom-width="0.1pt"
					border-bottom-style="solid"
					border-bottom-color="#dc2300"
					font-family="Helvetica">
		</fo:block>

		<fo:block
					space-before="7pt"
					space-before.conditionality="retain"
					font-size="8px"
					font-family="Helvetica">
			O&amp;Uuml; Struktuur Meedia - P&amp;auml;rnu maantee 154, 11317 Tallinn, Eesti
		</fo:block>

		<fo:block
					font-size="8px"
					font-family="Helvetica">
			Tel. +372 655 8334 - E-post: <fo:inline text-decoration="underline" color="#dc2300">info@struktuur.ee</fo:inline> - <fo:inline text-decoration="underline" color="#dc2300">http://www.struktuur.ee</fo:inline>
		</fo:block>
		<fo:block
					font-size="8px"
					font-family="Helvetica">
			Reg. Nr. 10593969 - A/s 221013523900 Hansapank, kood 787
		</fo:block>
	</fo:static-content>

<!--			<fo:external-graphic src="url('http://intranet.automatweb.com/orb.aw/class=image/action=show/fastcall=1/file=c23df764837bc5d7da21c950b4825cd8.gif')"
								 content-height="40px" content-width="200px"
			/>-->

    <fo:flow flow-name="xsl-region-body">
      <fo:block 
				font-family="Helvetica" 
				font-size="20px" 
				font-weight="bold" 
				text-align="center"
				space-before="170pt"
				space-before.conditionality="retain">Pakkumine</fo:block>
      <fo:block 
				font-family="Helvetica" 
				font-size="16px" 
				font-weight="bold" 
				text-align="center"
				space-before="50pt"
				space-before.conditionality="retain">{VAR:orderer}</fo:block>
      <fo:block 
				font-family="Helvetica" 
				font-size="16px" 
				font-weight="bold" 
				text-align="center"
				space-before="10pt"
				space-before.conditionality="retain">{VAR:name}</fo:block>


      <fo:block 
				font-family="Helvetica" 
				font-size="15px" 
				font-weight="bold" 
				text-align="right"
				space-before="120pt"
				space-before.conditionality="retain">{VAR:implementor}</fo:block>
      <fo:block 
				font-family="Helvetica" 
				font-size="13px" 
				text-align="right"
				space-before="10pt"
				space-before.conditionality="retain">{VAR:date}</fo:block>

      <fo:block text-align="center" 
				space-before="30pt"
				space-before.conditionality="retain">
		<fo:external-graphic src="url('{VAR:logo}')"/>
		<fo:external-graphic src="url('http://intranet.automatweb.com/orb.aw/class=image/action=show/fastcall=1/file=c23df764837bc5d7da21c950b4825cd8.gif')"
							 content-height="40px" content-width="200px"
		/>
	  </fo:block>

      <fo:block 
				font-family="Helvetica" 
				font-size="10px" 
				text-align="center"
				space-before="50pt"
				space-before.conditionality="retain"
				color="grey">
			Kogu k&amp;auml;esolevas pakkumises sisalduv informatsioon on konfidentsiaalne ning ei kuulu avaldamisele kolmandatele osapooltele!
		</fo:block>

		<fo:block break-before="page"/>

		<!-- sisukord -->
		<fo:block 
				  color="#dc2300" 
				  font-size="16px" 
				  font-family="Helvetica"
				  space-after="10pt">
			Sisukord
		</fo:block>
			
			<!-- SUB: CONTENTS_ENTRY -->
				<fo:block
					text-align-last="justify"
					font-family="Helvetica" 
					font-size="10px" 
					
					>
					{VAR:ch_name}
					<fo:leader leader-pattern="dots"/>
					<fo:page-number-citation ref-id="ch{VAR:ch_id}"/>
				</fo:block>
			<!-- END SUB: CONTENTS_ENTRY -->

		<fo:block break-before="page"/>
		
		<!-- p2ris sisu -->
		{VAR:content}

		<fo:block id="theEnd"/>
    </fo:flow>
  </fo:page-sequence>
</fo:root>
