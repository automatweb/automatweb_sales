<h2>Database analyzer</h2>
Analüüsitakse eelnevalt valmisgenereeritud XML dumpe andmebaasidest. Kood selle genereerimiseks on küll aw_dev koodis
olemas, kuid hetkel puudub talle kasutajaliides.<p>
<b>Legend:</b>
Kui "tüüp" lahter on värvitud punaseks, siis on andmebaasides selle välja tüübid erinevad, mis võib põhjustada probleeme.
Kui lahter on tühi, siis on see väli üldse defineerimata. Kui kõik ühe tabeli "tüüp" väljad on tühjad, siis puudub andmebaasist
selle tabeli definitsioon.<p>
Indeks lahtris näidatakse, kas tabelil on selle välja peale indeks tehtud (kiirendab otsimist ja kirjete lugemist _oluliselt_),
PRI on primaarne indeks, MUL on sekundaarne, tühi lahter näitab indeksi puudumist. Punane taust märgib probleemi, ühes andmebaasis
on indeks tehtud, teises pole, tulemuseks on see, et operatsioonid ilma indeksita väljal on märgatavalt aeglasemad.
<p>
Special väljal on kujutatud lisainfo välja kohta, see voiks ka baaside vahel ühtida, kuid kui see nii pole, siis ei teki
sellest ka mingeid erilis probleeme.
<p>
Vaadake see tabel hoolikalt üle, märgistage väljad, mida soovite ka lokaalsesse baasi tekitada, ning seejärel vajutage
nuppu "Process". <b><font color="red">Soovitav on enne teha varukoopia baasist</font></b>

<table border="1" width="100%">
<form method="POST" action="{VAR:baseurl}/orb{VAR:ext}">
<!-- SUB: block -->
<tr>
<td bgcolor=#aaffcc>&nbsp;</td>
<td colspan=3 align=center bgcolor=#aaffcc><b>Doonor: {VAR:name}</b></td>
<td colspan=4 align=center bgcolor=#aaffcc><b>Lokaalne baas: {VAR:name}</b></td>
</tr>
<tr>
<td bgcolor=#aaffcc><i>Väli</i></td>
<td bgcolor=#aaffcc><i>Tüüp</i></td>
<td bgcolor=#aaffcc><i>Indeks</i></td>
<td bgcolor=#aaffcc><i>Special</i></td>
<td bgcolor=#aaffcc><i>Mark</i></td>
<td bgcolor=#aaffcc><i>Tüüp</i></td>
<td bgcolor=#aaffcc><i>Indeks</i></td>
<td bgcolor=#aaffcc><i>Special</i></td>
</tr>
<!-- SUB: line -->
<tr>
<td><b>{VAR:key}</b></td>
<td bgcolor="{VAR:color1}">{VAR:type1}&nbsp;</td>
<td bgcolor="{VAR:color2}">{VAR:key1}&nbsp;</td>
<td bgcolor="{VAR:color3}">{VAR:flags1}&nbsp;</td>
<td bgcolor="#FFFFFF" align="center"><input type="checkbox" name="check[{VAR:name}][{VAR:key}]" {VAR:checked}></td>
<td bgcolor="{VAR:color1}">{VAR:type2}&nbsp;</td>
<td bgcolor="{VAR:color2}">{VAR:key2}&nbsp;</td>
<td bgcolor="{VAR:color3}">{VAR:flags2}&nbsp;</td>
</tr>
<!-- END SUB: line -->

<!-- END SUB: block -->
</table>
<input type="submit" value="    Process      ">
{VAR:reforb}
</form>
