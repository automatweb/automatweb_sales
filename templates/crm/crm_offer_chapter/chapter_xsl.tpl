		<fo:block 
				  color="#dc2300" 
				  font-size="16px" 
				  font-family="Helvetica"
				  id="ch{VAR:ch_id}"
				  space-after="10pt">
			{VAR:title}
		</fo:block>

		<fo:block 
					space-after="50pt" 
					font-family="Helvetica"
					font-size="10px"
					linefeed-treatment="preserve"
					white-space-collapse="false">{VAR:content}</fo:block>