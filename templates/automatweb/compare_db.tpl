<h2>Database analyzer</h2>
Anal��sitakse eelnevalt valmisgenereeritud XML dumpe andmebaasidest. Kood selle genereerimiseks on k�ll aw_dev koodis
olemas, kuid hetkel puudub talle kasutajaliides.<p>
<b>Legend:</b>
Kui "t��p" lahter on v�rvitud punaseks, siis on andmebaasides selle v�lja t��bid erinevad, mis v�ib p�hjustada probleeme.
Kui lahter on t�hi, siis on see v�li �ldse defineerimata. Kui k�ik �he tabeli "t��p" v�ljad on t�hjad, siis puudub andmebaasist
selle tabeli definitsioon.<p>
Indeks lahtris n�idatakse, kas tabelil on selle v�lja peale indeks tehtud (kiirendab otsimist ja kirjete lugemist _oluliselt_),
PRI on primaarne indeks, MUL on sekundaarne, t�hi lahter n�itab indeksi puudumist. Punane taust m�rgib probleemi, �hes andmebaasis
on indeks tehtud, teises pole, tulemuseks on see, et operatsioonid ilma indeksita v�ljal on m�rgatavalt aeglasemad.
<p>
Special v�ljal on kujutatud lisainfo v�lja kohta, see voiks ka baaside vahel �htida, kuid kui see nii pole, siis ei teki
sellest ka mingeid erilis probleeme.
<p>
Vaadake see tabel hoolikalt �le, m�rgistage v�ljad, mida soovite ka lokaalsesse baasi tekitada, ning seej�rel vajutage
nuppu "Process". <b><font color="red">Soovitav on enne teha varukoopia baasist</font></b>

<table border="1" width="100%">
<form method="POST" action="{VAR:baseurl}/orb.{VAR:ext}">
<!-- SUB: block -->
<tr>
<td bgcolor=#aaffcc>&nbsp;</td>
<td colspan=3 align=center bgcolor=#aaffcc><b>Doonor: {VAR:name}</b></td>
<td colspan=4 align=center bgcolor=#aaffcc><b>Lokaalne baas: {VAR:name}</b></td>
</tr>
<tr>
<td bgcolor=#aaffcc><i>V�li</i></td>
<td bgcolor=#aaffcc><i>T��p</i></td>
<td bgcolor=#aaffcc><i>Indeks</i></td>
<td bgcolor=#aaffcc><i>Special</i></td>
<td bgcolor=#aaffcc><i>Mark</i></td>
<td bgcolor=#aaffcc><i>T��p</i></td>
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
