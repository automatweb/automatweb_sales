			<h2>{VAR:title}</h2>
			<form method="POST" action="/orb.aw">
			<!-- SUB: rating -->
				<b>{VAR:rating_caption}:</b><br>
			<!-- SUB: rating_value -->
			<input type='radio' name='{VAR:rating_value_name}' value='{VAR:rating_value_value}'>{VAR:rating_value_caption}</input><br>
			<!-- END SUB: rating_value -->
			<!-- END SUB: rating -->
			{VAR:rating_form_vars}
			</form>
