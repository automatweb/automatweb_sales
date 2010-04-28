<?php

namespace automatweb;
// calendar_registration_form.aw - Kalendri s&uuml;ndmusele registreerimise vorm
/*

@classinfo syslog_type=ST_CALENDAR_REGISTRATION_FORM relationmgr=yes no_comment=1 no_status=1 maintainer=kristo

@default table=objects
@default group=general
@default field=meta
@default method=serialize

@property firstname type=textbox
@caption Eesnimi

@property lastname type=textbox
@caption Perekonnanimi

@property co_name type=textbox
@caption Ettev&otilde;tte nimi

@property address type=textbox
@caption Aadress

@property phone type=textbox
@caption Telefon

@property fax type=textbox
@caption Faks

@property email type=textbox
@caption E-post

@property comment type=textarea cols=50 rows=5
@caption Kommentaar

@property person_id type=hidden


@groupinfo data caption="Andmed"
@default group=data

@property userch1 type=checkbox
@caption User-defined checkbox 1

@property user1 type=textbox
@caption User-defined textbox 1

@property user2 type=textbox
@caption User-defined textbox 2

@property user3 type=textbox
@caption User-defined textbox 3

@property user4 type=textbox
@caption User-defined textbox 4

@property user5 type=textbox
@caption User-defined textbox 5

@property userta1 type=textarea rows=5 cols=30
@caption User-defined textarea 1

@property userta2 type=textarea rows=5 cols=30
@caption User-defined textarea 2

@property userta3 type=textarea rows=5 cols=30
@caption User-defined textarea 3

@property userta4 type=textarea rows=5 cols=30
@caption User-defined textarea 4

@property userta5 type=textarea rows=5 cols=30
@caption User-defined textarea 5

@property uservar1 type=classificator reltype=RELTYPE_VARUSER1 store=connect
@caption User-defined var 1

@property uservar2 type=classificator reltype=RELTYPE_VARUSER2 store=connect
@caption User-defined var 2

@property uservar3 type=classificator reltype=RELTYPE_VARUSER3 store=connect
@caption User-defined var 3

@property uservar4 type=classificator reltype=RELTYPE_VARUSER4 store=connect
@caption User-defined var 4

@property uservar5 type=classificator reltype=RELTYPE_VARUSER5 store=connect
@caption User-defined var 5

@property uservar6 type=classificator reltype=RELTYPE_VARUSER6 store=connect
@caption User-defined var 6

@property uservar7 type=classificator reltype=RELTYPE_VARUSER7 store=connect
@caption User-defined var 7

@property uservar8 type=classificator reltype=RELTYPE_VARUSER8 store=connect
@caption User-defined var 8

@property uservar9 type=classificator reltype=RELTYPE_VARUSER9 store=connect
@caption User-defined var 9

@property uservar10 type=classificator reltype=RELTYPE_VARUSER10 store=connect
@caption User-defined var 10

@property userdate1 type=date_select year_from=1970 year_to=2020
@caption User-defined date select 1

@property userdate2 type=date_select year_from=1970 year_to=2020
@caption User-defined date select 2

@property userdate3 type=date_select year_from=1970 year_to=2020
@caption User-defined date select 3

@property userdate4 type=date_select year_from=1970 year_to=2020
@caption User-defined date select 4

@property userdate5 type=date_select year_from=1970 year_to=2020
@caption User-defined date select 5

@property userch1 type=checkbox ch_value=1 datatype=int
@caption User-defined checkbox 1

@property userch2 type=checkbox ch_value=1 datatype=int
@caption User-defined checkbox 2

@property userch3 type=checkbox ch_value=1 datatype=int
@caption User-defined checkbox 3

@property userch4 type=checkbox ch_value=1 datatype=int
@caption User-defined checkbox 4

@property userch5 type=checkbox ch_value=1 datatype=int
@caption User-defined checkbox 5

@property usersubtitle1 type=text store=no subtitle=1
@caption Subtitle1

@property usersubtitle2 type=text store=no subtitle=1
@caption Subtitle2

@property usersubtitle3 type=text store=no subtitle=1
@caption Subtitle3

@property usersubtitle4 type=text store=no subtitle=1
@caption Subtitle4

@property usersubtitle5 type=text store=no subtitle=1
@caption Subtitle5

@property usertext1 type=text
@caption Usertext1

@property usertext2 type=text
@caption Usertext2

@property usertext3 type=text
@caption Usertext3

@property usertext4 type=text
@caption Usertext4

@property usertext5 type=text
@caption Usertext5

@property usertext6 type=text store=no
@caption Usertext6

@property usertext7 type=text store=no
@caption Usertext7

@property usertext8 type=text store=no
@caption Usertext8

@property usertext9 type=text store=no
@caption Usertext9

@property usertext10 type=text store=no
@caption Usertext10

@property usersubmit1 type=submit store=no
@caption User-defined submit 1

@property userreset1 type=reset store=no
@caption User-defined reset 1

@property userprint1 type=button store=no
@caption User-defined print 1


@property udefhidden1 type=hidden
@property udefhidden2 type=hidden
@property udefhidden3 type=hidden

---------------- textbox ----------------

@groupinfo textboxes caption="Textbox"
@default group=textboxes

@property user6 type=textbox
@caption User-defined 6

@property user7 type=textbox
@caption User-defined 7

@property user8 type=textbox
@caption User-defined 8

@property user9 type=textbox
@caption User-defined 9

@property user10 type=textbox
@caption User-defined 10

@property user11 type=textbox
@caption User-defined 11

@property user12 type=textbox
@caption User-defined 12

@property user13 type=textbox
@caption User-defined 13

@property user14 type=textbox
@caption User-defined 14

@property user15 type=textbox
@caption User-defined 15

@property user16 type=textbox
@caption User-defined 16

@property user17 type=textbox
@caption User-defined 17

@property user18 type=textbox
@caption User-defined 18

@property user19 type=textbox
@caption User-defined 19

@property user20 type=textbox
@caption User-defined 20

@property user21 type=textbox
@caption User-defined 21

@property user22 type=textbox
@caption User-defined 22

@property user23 type=textbox
@caption User-defined 23

@property user24 type=textbox
@caption User-defined 24

@property user25 type=textbox
@caption User-defined 25

@property user26 type=textbox
@caption User-defined 26

@property user27 type=textbox
@caption User-defined 27

@property user28 type=textbox
@caption User-defined 28

@property user29 type=textbox
@caption User-defined 29

@property user30 type=textbox
@caption User-defined 30

@property user31 type=textbox
@caption User-defined 31

@property user32 type=textbox
@caption User-defined 32

@property user33 type=textbox
@caption User-defined 33

@property user34 type=textbox
@caption User-defined 34

@property user35 type=textbox
@caption User-defined 35

@property user36 type=textbox
@caption User-defined 36

@property user37 type=textbox
@caption User-defined 37

@property user38 type=textbox
@caption User-defined 28

@property user39 type=textbox
@caption User-defined 39

@property user40 type=textbox
@caption User-defined 40

@property user41 type=textbox
@caption User-defined 41

@property user42 type=textbox
@caption User-defined 42

@property user43 type=textbox
@caption User-defined 43

@property user44 type=textbox
@caption User-defined 44

@property user45 type=textbox
@caption User-defined 45

@property user46 type=textbox
@caption User-defined 46

@property user47 type=textbox
@caption User-defined 47

@property user48 type=textbox
@caption User-defined 48

@property user49 type=textbox
@caption User-defined 49

@property user50 type=textbox
@caption User-defined 50

---------------- textarea ----------------

@groupinfo textareas caption="Textarea"
@default group=textareas

@property userta6 type=textarea
@caption User-defined ta 6

@property userta7 type=textarea
@caption User-defined ta 7

@property userta8 type=textarea
@caption User-defined ta 8

@property userta9 type=textarea
@caption User-defined ta 9

@property userta10 type=textarea
@caption User-defined ta 10

@property userta11 type=textarea
@caption User-defined ta 11

@property userta12 type=textarea
@caption User-defined ta 12

@property userta13 type=textarea
@caption User-defined ta 13

@property userta14 type=textarea
@caption User-defined ta 14

@property userta15 type=textarea
@caption User-defined ta 15

@property userta16 type=textarea
@caption User-defined ta 16

@property userta17 type=textarea
@caption User-defined ta 17

@property userta18 type=textarea
@caption User-defined ta 18

@property userta19 type=textarea
@caption User-defined ta 19

@property userta20 type=textarea
@caption User-defined ta 20

@property userta21 type=textarea
@caption User-defined ta 21

@property userta22 type=textarea
@caption User-defined ta 22

@property userta23 type=textarea
@caption User-defined ta 23

@property userta24 type=textarea
@caption User-defined ta 24

@property userta25 type=textarea
@caption User-defined ta 25

@property userta26 type=textarea
@caption User-defined ta 26

@property userta27 type=textarea
@caption User-defined ta 27

@property userta28 type=textarea
@caption User-defined ta 28

@property userta29 type=textarea
@caption User-defined ta 29

@property userta30 type=textarea
@caption User-defined ta 30

@property userta31 type=textarea
@caption User-defined ta 31

@property userta32 type=textarea
@caption User-defined ta 32

@property userta33 type=textarea
@caption User-defined ta 33

@property userta34 type=textarea
@caption User-defined ta 34

@property userta35 type=textarea
@caption User-defined ta 35

@property userta36 type=textarea
@caption User-defined ta 36

@property userta37 type=textarea
@caption User-defined ta 37

@property userta38 type=textarea
@caption User-defined ta 38

@property userta39 type=textarea
@caption User-defined ta 39

@property userta40 type=textarea
@caption User-defined ta 40

@property userta41 type=textarea
@caption User-defined ta 41

@property userta42 type=textarea
@caption User-defined ta 42

@property userta43 type=textarea
@caption User-defined ta 43

@property userta44 type=textarea
@caption User-defined ta 44

@property userta45 type=textarea
@caption User-defined ta 45

@property userta46 type=textarea
@caption User-defined ta 46

@property userta47 type=textarea
@caption User-defined ta 47

@property userta48 type=textarea
@caption User-defined ta 48

@property userta49 type=textarea
@caption User-defined ta 49

@property userta50 type=textarea
@caption User-defined ta 50

---------------- classificator ----------------

@groupinfo classificators caption="Classificator"
@default group=classificators

@property uservar11 type=classificator reltype=RELTYPE_VARUSER11 store=connect
@caption User-defined var 11

@property uservar12 type=classificator reltype=RELTYPE_VARUSER12 store=connect
@caption User-defined var 12

@property uservar13 type=classificator reltype=RELTYPE_VARUSER13 store=connect
@caption User-defined var 13

@property uservar14 type=classificator reltype=RELTYPE_VARUSER14 store=connect
@caption User-defined var 14

@property uservar15 type=classificator reltype=RELTYPE_VARUSER15 store=connect
@caption User-defined var 15

@property uservar16 type=classificator reltype=RELTYPE_VARUSER16 store=connect
@caption User-defined var 16

@property uservar17 type=classificator reltype=RELTYPE_VARUSER17 store=connect
@caption User-defined var 17

@property uservar18 type=classificator reltype=RELTYPE_VARUSER18 store=connect
@caption User-defined var 18

@property uservar19 type=classificator reltype=RELTYPE_VARUSER19 store=connect
@caption User-defined var 19

@property uservar20 type=classificator reltype=RELTYPE_VARUSER20 store=connect
@caption User-defined var 20

@property uservar21 type=classificator reltype=RELTYPE_VARUSER21 store=connect
@caption User-defined var 21

@property uservar22 type=classificator reltype=RELTYPE_VARUSER22 store=connect
@caption User-defined var 22

@property uservar23 type=classificator reltype=RELTYPE_VARUSER23 store=connect
@caption User-defined var 23

@property uservar24 type=classificator reltype=RELTYPE_VARUSER24 store=connect
@caption User-defined var 24

@property uservar25 type=classificator reltype=RELTYPE_VARUSER25 store=connect
@caption User-defined var 25

@property uservar26 type=classificator reltype=RELTYPE_VARUSER26 store=connect
@caption User-defined var 26

@property uservar27 type=classificator reltype=RELTYPE_VARUSER27 store=connect
@caption User-defined var 27

@property uservar28 type=classificator reltype=RELTYPE_VARUSER28 store=connect
@caption User-defined var 28

@property uservar29 type=classificator reltype=RELTYPE_VARUSER29 store=connect
@caption User-defined var 29

@property uservar30 type=classificator reltype=RELTYPE_VARUSER30 store=connect
@caption User-defined var 30

@property uservar31 type=classificator reltype=RELTYPE_VARUSER31 store=connect
@caption User-defined var 31

@property uservar32 type=classificator reltype=RELTYPE_VARUSER32 store=connect
@caption User-defined var 32

@property uservar33 type=classificator reltype=RELTYPE_VARUSER33 store=connect
@caption User-defined var 33

@property uservar34 type=classificator reltype=RELTYPE_VARUSER34 store=connect
@caption User-defined var 34

@property uservar35 type=classificator reltype=RELTYPE_VARUSER35 store=connect
@caption User-defined var 35

@property uservar35 type=classificator reltype=RELTYPE_VARUSER35 store=connect
@caption User-defined var 35

@property uservar36 type=classificator reltype=RELTYPE_VARUSER36 store=connect
@caption User-defined var 36

@property uservar37 type=classificator reltype=RELTYPE_VARUSER37 store=connect
@caption User-defined var 37

@property uservar38 type=classificator reltype=RELTYPE_VARUSER38 store=connect
@caption User-defined var 38

@property uservar39 type=classificator reltype=RELTYPE_VARUSER39 store=connect
@caption User-defined var 39

@property uservar40 type=classificator reltype=RELTYPE_VARUSER40 store=connect
@caption User-defined var 40

@property uservar41 type=classificator reltype=RELTYPE_VARUSER41 store=connect
@caption User-defined var 41

@property uservar42 type=classificator reltype=RELTYPE_VARUSER42 store=connect
@caption User-defined var 42

@property uservar43 type=classificator reltype=RELTYPE_VARUSER43 store=connect
@caption User-defined var 43

@property uservar44 type=classificator reltype=RELTYPE_VARUSER44 store=connect
@caption User-defined var 44

@property uservar45 type=classificator reltype=RELTYPE_VARUSER45 store=connect
@caption User-defined var 45

@property uservar46 type=classificator reltype=RELTYPE_VARUSER46 store=connect
@caption User-defined var 46

@property uservar47 type=classificator reltype=RELTYPE_VARUSER47 store=connect
@caption User-defined var 47

@property uservar48 type=classificator reltype=RELTYPE_VARUSER48 store=connect
@caption User-defined var 48

@property uservar49 type=classificator reltype=RELTYPE_VARUSER49 store=connect
@caption User-defined var 49

@property uservar50 type=classificator reltype=RELTYPE_VARUSER50 store=connect
@caption User-defined var 50

@property uservar51 type=classificator reltype=RELTYPE_VARUSER51 store=connect
@caption User-defined var 51

@property uservar52 type=classificator reltype=RELTYPE_VARUSER52 store=connect
@caption User-defined var 52

@property uservar53 type=classificator reltype=RELTYPE_VARUSER53 store=connect
@caption User-defined var 53

@property uservar54 type=classificator reltype=RELTYPE_VARUSER54 store=connect
@caption User-defined var 54

@property uservar55 type=classificator reltype=RELTYPE_VARUSER55 store=connect
@caption User-defined var 55

@property uservar56 type=classificator reltype=RELTYPE_VARUSER57 store=connect
@caption User-defined var 56

@property uservar58 type=classificator reltype=RELTYPE_VARUSER58 store=connect
@caption User-defined var 58

@property uservar59 type=classificator reltype=RELTYPE_VARUSER59 store=connect
@caption User-defined var 59

@property uservar60 type=classificator reltype=RELTYPE_VARUSER60 store=connect
@caption User-defined var 60

@property uservar61 type=classificator reltype=RELTYPE_VARUSER61 store=connect
@caption User-defined var 61

@property uservar62 type=classificator reltype=RELTYPE_VARUSER62 store=connect
@caption User-defined var 62

@property uservar63 type=classificator reltype=RELTYPE_VARUSER63 store=connect
@caption User-defined var 63

@property uservar64 type=classificator reltype=RELTYPE_VARUSER64 store=connect
@caption User-defined var 64

@property uservar65 type=classificator reltype=RELTYPE_VARUSER65 store=connect
@caption User-defined var 65

@property uservar66 type=classificator reltype=RELTYPE_VARUSER66 store=connect
@caption User-defined var 66

@property uservar67 type=classificator reltype=RELTYPE_VARUSER67 store=connect
@caption User-defined var 67

@property uservar68 type=classificator reltype=RELTYPE_VARUSER68 store=connect
@caption User-defined var 68

@property uservar69 type=classificator reltype=RELTYPE_VARUSER69 store=connect
@caption User-defined var 69

@property uservar70 type=classificator reltype=RELTYPE_VARUSER70 store=connect
@caption User-defined var 70

@property uservar71 type=classificator reltype=RELTYPE_VARUSER71 store=connect
@caption User-defined var 71

@property uservar72 type=classificator reltype=RELTYPE_VARUSER72 store=connect
@caption User-defined var 72

@property uservar73 type=classificator reltype=RELTYPE_VARUSER73 store=connect
@caption User-defined var 73

@property uservar74 type=classificator reltype=RELTYPE_VARUSER74 store=connect
@caption User-defined var 74

@property uservar75 type=classificator reltype=RELTYPE_VARUSER75 store=connect
@caption User-defined var 75

@property uservar76 type=classificator reltype=RELTYPE_VARUSER76 store=connect
@caption User-defined var 76

@property uservar77 type=classificator reltype=RELTYPE_VARUSER77 store=connect
@caption User-defined var 77

@property uservar78 type=classificator reltype=RELTYPE_VARUSER78 store=connect
@caption User-defined var 78

@property uservar79 type=classificator reltype=RELTYPE_VARUSER79 store=connect
@caption User-defined var 79

@property uservar80 type=classificator reltype=RELTYPE_VARUSER80 store=connect
@caption User-defined var 80

---------------- text ----------------

@groupinfo texts caption="Text"
@default group=texts

@property usertext11 type=text store=no
@caption Usertext11

@property usertext12 type=text store=no
@caption Usertext12

@property usertext13 type=text store=no
@caption Usertext13

@property usertext14 type=text store=no
@caption Usertext14

@property usertext15 type=text store=no
@caption Usertext15

@property usertext16 type=text store=no
@caption Usertext16

@property usertext17 type=text store=no
@caption Usertext17

@property usertext18 type=text store=no
@caption Usertext18

@property usertext19 type=text store=no
@caption Usertext19

@property usertext20 type=text store=no
@caption Usertext20

@property usertext21 type=text store=no
@caption Usertext21

@property usertext22 type=text store=no
@caption Usertext22

@property usertext23 type=text store=no
@caption Usertext23

@property usertext24 type=text store=no
@caption Usertext24

@property usertext25 type=text store=no
@caption Usertext25

@property usertext26 type=text store=no
@caption Usertext26

@property usertext27 type=text store=no
@caption Usertext27

@property usertext28 type=text store=no
@caption Usertext28

@property usertext29 type=text store=no
@caption Usertext29

@property usertext30 type=text store=no
@caption Usertext30

@property usertext31 type=text store=no
@caption Usertext31

@property usertext32 type=text store=no
@caption Usertext32

@property usertext33 type=text store=no
@caption Usertext33

@property usertext34 type=text store=no
@caption Usertext34

@property usertext35 type=text store=no
@caption Usertext35

@property usertext36 type=text store=no
@caption Usertext36

@property usertext37 type=text store=no
@caption Usertext38

@property usertext39 type=text store=no
@caption Usertext39

@property usertext40 type=text store=no
@caption Usertext40

@property usertext41 type=text store=no
@caption Usertext41

@property usertext42 type=text store=no
@caption Usertext42

@property usertext43 type=text store=no
@caption Usertext43

@property usertext44 type=text store=no
@caption Usertext44

@property usertext45 type=text store=no
@caption Usertext45

@property usertext46 type=text store=no
@caption Usertext46

@property usertext47 type=text store=no
@caption Usertext47

@property usertext48 type=text store=no
@caption Usertext48

@property usertext49 type=text store=no
@caption Usertext49

@property usertext50 type=text store=no
@caption Usertext50

---------------- seosed ----------------

@reltype VARUSER1 value=100 clid=CL_META
@caption kasutajadefineeritud muutuja 1

@reltype VARUSER2 value=200 clid=CL_META
@caption kasutajadefineeritud muutuja 2

@reltype VARUSER3 value=300 clid=CL_META
@caption kasutajadefineeritud muutuja 3

@reltype VARUSER4 value=4 clid=CL_META
@caption kasutajadefineeritud muutuja 4

@reltype VARUSER5 value=5 clid=CL_META
@caption kasutajadefineeritud muutuja 5

@reltype VARUSER6 value=6 clid=CL_META
@caption kasutajadefineeritud muutuja 6

@reltype VARUSER7 value=7 clid=CL_META
@caption kasutajadefineeritud muutuja 7

@reltype VARUSER8 value=8 clid=CL_META
@caption kasutajadefineeritud muutuja 8

@reltype VARUSER9 value=9 clid=CL_META
@caption kasutajadefineeritud muutuja 9

@reltype VARUSER10 value=10 clid=CL_META
@caption kasutajadefineeritud muutuja 10

@reltype VARUSER11 value=11 clid=CL_META
@caption kasutajadefineeritud muutuja 11

@reltype VARUSER12 value=12 clid=CL_META
@caption kasutajadefineeritud muutuja 12

@reltype VARUSER13 value=13 clid=CL_META
@caption kasutajadefineeritud muutuja 13

@reltype VARUSER14 value=14 clid=CL_META
@caption kasutajadefineeritud muutuja 14

@reltype VARUSER15 value=15 clid=CL_META
@caption kasutajadefineeritud muutuja 15

@reltype VARUSER16 value=16 clid=CL_META
@caption kasutajadefineeritud muutuja 16

@reltype VARUSER17 value=17 clid=CL_META
@caption kasutajadefineeritud muutuja 17

@reltype VARUSER18 value=18 clid=CL_META
@caption kasutajadefineeritud muutuja 18

@reltype VARUSER19 value=19 clid=CL_META
@caption kasutajadefineeritud muutuja 19

@reltype VARUSER20 value=20 clid=CL_META
@caption kasutajadefineeritud muutuja 20

@reltype VARUSER21 value=21 clid=CL_META
@caption kasutajadefineeritud muutuja 21

@reltype VARUSER22 value=22 clid=CL_META
@caption kasutajadefineeritud muutuja 22

@reltype VARUSER23 value=23 clid=CL_META
@caption kasutajadefineeritud muutuja 23

@reltype VARUSER24 value=24 clid=CL_META
@caption kasutajadefineeritud muutuja 24

@reltype VARUSER25 value=25 clid=CL_META
@caption kasutajadefineeritud muutuja 25

@reltype VARUSER26 value=26 clid=CL_META
@caption kasutajadefineeritud muutuja 26

@reltype VARUSER27 value=27 clid=CL_META
@caption kasutajadefineeritud muutuja 27

@reltype VARUSER28 value=28 clid=CL_META
@caption kasutajadefineeritud muutuja 28

@reltype VARUSER29 value=29 clid=CL_META
@caption kasutajadefineeritud muutuja 29

@reltype VARUSER30 value=30 clid=CL_META
@caption kasutajadefineeritud muutuja 30

@reltype VARUSER31 value=31 clid=CL_META
@caption kasutajadefineeritud muutuja 31

@reltype VARUSER32 value=32 clid=CL_META
@caption kasutajadefineeritud muutuja 32

@reltype VARUSER33 value=33 clid=CL_META
@caption kasutajadefineeritud muutuja 33

@reltype VARUSER34 value=34 clid=CL_META
@caption kasutajadefineeritud muutuja 34

@reltype VARUSER35 value=35 clid=CL_META
@caption kasutajadefineeritud muutuja 35

@reltype VARUSER36 value=36 clid=CL_META
@caption kasutajadefineeritud muutuja 36

@reltype VARUSER37 value=37 clid=CL_META
@caption kasutajadefineeritud muutuja 37

@reltype VARUSER38 value=38 clid=CL_META
@caption kasutajadefineeritud muutuja 38

@reltype VARUSER39 value=39 clid=CL_META
@caption kasutajadefineeritud muutuja 39

@reltype VARUSER40 value=40 clid=CL_META
@caption kasutajadefineeritud muutuja 40

@reltype VARUSER41 value=41 clid=CL_META
@caption kasutajadefineeritud muutuja 41

@reltype VARUSER42 value=42 clid=CL_META
@caption kasutajadefineeritud muutuja 42

@reltype VARUSER43 value=43 clid=CL_META
@caption kasutajadefineeritud muutuja 43

@reltype VARUSER44 value=44 clid=CL_META
@caption kasutajadefineeritud muutuja 44

@reltype VARUSER45 value=45 clid=CL_META
@caption kasutajadefineeritud muutuja 45

@reltype VARUSER46 value=46 clid=CL_META
@caption kasutajadefineeritud muutuja 46

@reltype VARUSER47 value=47 clid=CL_META
@caption kasutajadefineeritud muutuja 47

@reltype VARUSER48 value=48 clid=CL_META
@caption kasutajadefineeritud muutuja 48

@reltype VARUSER49 value=49 clid=CL_META
@caption kasutajadefineeritud muutuja 49

@reltype VARUSER50 value=50 clid=CL_META
@caption kasutajadefineeritud muutuja 50

@reltype VARUSER51 value=51 clid=CL_META
@caption kasutajadefineeritud muutuja 51

@reltype VARUSER52 value=52 clid=CL_META
@caption kasutajadefineeritud muutuja 52

@reltype VARUSER53 value=53 clid=CL_META
@caption kasutajadefineeritud muutuja 53

@reltype VARUSER54 value=54 clid=CL_META
@caption kasutajadefineeritud muutuja 54

@reltype VARUSER55 value=55 clid=CL_META
@caption kasutajadefineeritud muutuja 55

@reltype VARUSER56 value=56 clid=CL_META
@caption kasutajadefineeritud muutuja 56

@reltype VARUSER57 value=57 clid=CL_META
@caption kasutajadefineeritud muutuja 57

@reltype VARUSER58 value=58 clid=CL_META
@caption kasutajadefineeritud muutuja 58

@reltype VARUSER59 value=59 clid=CL_META
@caption kasutajadefineeritud muutuja 59

@reltype VARUSER60 value=60 clid=CL_META
@caption kasutajadefineeritud muutuja 60

@reltype VARUSER61 value=61 clid=CL_META
@caption kasutajadefineeritud muutuja 61

@reltype VARUSER62 value=62 clid=CL_META
@caption kasutajadefineeritud muutuja 62

@reltype VARUSER63 value=63 clid=CL_META
@caption kasutajadefineeritud muutuja 63

@reltype VARUSER64 value=64 clid=CL_META
@caption kasutajadefineeritud muutuja 64

@reltype VARUSER65 value=65 clid=CL_META
@caption kasutajadefineeritud muutuja 65

@reltype VARUSER66 value=66 clid=CL_META
@caption kasutajadefineeritud muutuja 66

@reltype VARUSER67 value=67 clid=CL_META
@caption kasutajadefineeritud muutuja 67

@reltype VARUSER68 value=68 clid=CL_META
@caption kasutajadefineeritud muutuja 68

@reltype VARUSER69 value=69 clid=CL_META
@caption kasutajadefineeritud muutuja 69

@reltype VARUSER70 value=70 clid=CL_META
@caption kasutajadefineeritud muutuja 70

@reltype VARUSER71 value=71 clid=CL_META
@caption kasutajadefineeritud muutuja 71

@reltype VARUSER72 value=72 clid=CL_META
@caption kasutajadefineeritud muutuja 72

@reltype VARUSER73 value=73 clid=CL_META
@caption kasutajadefineeritud muutuja 73

@reltype VARUSER74 value=74 clid=CL_META
@caption kasutajadefineeritud muutuja 74

@reltype VARUSER75 value=75 clid=CL_META
@caption kasutajadefineeritud muutuja 75

@reltype VARUSER76 value=76 clid=CL_META
@caption kasutajadefineeritud muutuja 76

@reltype VARUSER77 value=77 clid=CL_META
@caption kasutajadefineeritud muutuja 77

@reltype VARUSER78 value=78 clid=CL_META
@caption kasutajadefineeritud muutuja 78

@reltype VARUSER79 value=79 clid=CL_META
@caption kasutajadefineeritud muutuja 79

@reltype VARUSER80 value=80 clid=CL_META
@caption kasutajadefineeritud muutuja 80

@reltype EVENT value=1 clid=CL_CRM_MEETING,CL_TASK,CL_CRM_CALL
@caption s&uuml;ndmus

@reltype OT value=2 clid=CL_OBJECT_TYPE
@caption registreerumisvormi t&uuml;&uuml;p

@reltype DATA value=3 clid=CL_CRM_MEETING,CL_TASK,CL_CRM_CALL
@caption andmed

*/

class calendar_registration_form extends class_base
{
	const AW_CLID = 848;

	function calendar_registration_form()
	{
		$this->init(array(
			"tpldir" => "applications/calendar/calendar_registration_form",
			"clid" => CL_CALENDAR_REGISTRATION_FORM
		));
	}
}
?>
