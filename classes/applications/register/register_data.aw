<?php
// register_data.aw - Registri andmed
/*
@classinfo relationmgr=yes no_comment=1
@default group=general
@default table=aw_register_data

@tableinfo aw_register_data index=aw_id master_table=objects master_index=brother_of

@property register_id type=hidden field=aw_register_id group=general

---------------- andmed ----------------

@groupinfo data caption="Andmed"
@default group=data

@property user1 type=textbox field=aw_user1
@caption User-defined 1

@property user2 type=textbox field=aw_user2
@caption User-defined 2

@property user3 type=text field=aw_user3
@caption User-defined 3

@property user4 type=textbox field=aw_user4
@caption User-defined 4

@property user5 type=textbox field=aw_user5
@caption User-defined 5

@property user6 type=textbox field=aw_user6
@caption User-defined 6

@property user7 type=textbox field=aw_user7
@caption User-defined 7

@property user8 type=textbox field=aw_user8
@caption User-defined 8

@property user9 type=textbox field=aw_user9
@caption User-defined 9

@property user10 type=textbox field=aw_user10
@caption User-defined 10

@property user11 type=textbox field=aw_user11
@caption User-defined 11

@property user12 type=textbox field=aw_user12
@caption User-defined 12

@property user13 type=textbox field=aw_user13
@caption User-defined 13

@property user14 type=textbox field=aw_user14
@caption User-defined 14

@property user15 type=textbox field=aw_user15
@caption User-defined 15

@property user16 type=textbox field=aw_user16
@caption User-defined 16

@property user17 type=textbox field=aw_user17
@caption User-defined 17

@property user18 type=textbox field=aw_user18
@caption User-defined 18

@property user19 type=textbox field=aw_user19
@caption User-defined 19

@property user20 type=textbox field=aw_user20
@caption User-defined 20

@property user21 type=textbox field=aw_user21
@caption User-defined 21

@property user22 type=textbox field=aw_user22
@caption User-defined 22

@property user23 type=textbox field=aw_user23
@caption User-defined 23

@property user24 type=textbox field=aw_user24
@caption User-defined 24

@property user25 type=textbox field=aw_user25
@caption User-defined 25

@property user26 type=textbox field=aw_user26
@caption User-defined 26

@property user27 type=textbox field=aw_user27
@caption User-defined 27

@property user28 type=textbox field=aw_user28
@caption User-defined 28

@property user29 type=textbox field=aw_user29
@caption User-defined 29

@property user30 type=textbox field=aw_user30
@caption User-defined 30

@property user31 type=textbox field=aw_user31
@caption User-defined 31

@property user32 type=textbox field=aw_user32
@caption User-defined 32

@property user33 type=textbox field=aw_user33
@caption User-defined 33

@property user34 type=textbox field=aw_user34
@caption User-defined 34

@property user35 type=textbox field=aw_user35
@caption User-defined 35

@property user36 type=textbox field=aw_user36
@caption User-defined 36

@property user37 type=textbox field=aw_user37
@caption User-defined 37

@property user38 type=textbox field=aw_user38
@caption User-defined 38

@property user39 type=textbox field=aw_user39
@caption User-defined 39

@property user40 type=textbox field=aw_user40
@caption User-defined 40

@property userta1 type=textarea field=aw_tauser1 rows=20 cols=50
@caption User-defined ta 1

@property userta2 type=textarea field=aw_tauser2 rows=20 cols=50
@caption User-defined ta 2

@property userta3 type=textarea field=aw_tauser3 rows=20 cols=50
@caption User-defined ta 3

@property userta4 type=textarea field=aw_tauser4 rows=20 cols=50
@caption User-defined ta 4

@property userta5 type=textarea field=aw_tauser5 rows=20 cols=50
@caption User-defined ta 5

@property userta6 type=textarea field=aw_tauser6 rows=20 cols=50
@caption User-defined ta 6

@property userta7 type=textarea field=aw_tauser7 rows=20 cols=50
@caption User-defined ta 7

@property userta8 type=textarea field=aw_tauser8 rows=20 cols=50
@caption User-defined ta 8

@property userta9 type=textarea field=aw_tauser9 rows=20 cols=50
@caption User-defined ta 9

@property userta10 type=textarea field=aw_tauser10 rows=20 cols=50
@caption User-defined ta 10

@property uservar1 type=classificator field=aw_varuser1 reltype=RELTYPE_VARUSER1 store=connect
@caption User-defined var 1

@property uservar2 type=classificator field=aw_varuser2 reltype=RELTYPE_VARUSER2 store=connect
@caption User-defined var 2

@property uservar3 type=classificator field=aw_varuser3 reltype=RELTYPE_VARUSER3 store=connect
@caption User-defined var 3

@property uservar4 type=classificator field=aw_varuser4 reltype=RELTYPE_VARUSER4 store=connect
@caption User-defined var 4

@property uservar5 type=classificator field=aw_varuser5 reltype=RELTYPE_VARUSER5 store=connect
@caption User-defined var 5

@property uservar6 type=classificator field=aw_varuser6 reltype=RELTYPE_VARUSER6 store=connect
@caption User-defined var 6

@property uservar7 type=classificator field=aw_varuser7 reltype=RELTYPE_VARUSER7 store=connect
@caption User-defined var 7

@property uservar8 type=classificator field=aw_varuser8 reltype=RELTYPE_VARUSER8 store=connect
@caption User-defined var 8

@property uservar9 type=classificator field=aw_varuser9 reltype=RELTYPE_VARUSER9 store=connect
@caption User-defined var 9

@property uservar10 type=classificator field=aw_varuser10 reltype=RELTYPE_VARUSER10 store=connect
@caption User-defined var 10

@property uservar11 type=classificator field=aw_varuser10 reltype=RELTYPE_VARUSER81 store=connect
@caption User-defined var 11

@property uservar12 type=classificator field=aw_varuser10 reltype=RELTYPE_VARUSER82 store=connect
@caption User-defined var 12

@property uservar13 type=classificator field=aw_varuser10 reltype=RELTYPE_VARUSER83 store=connect
@caption User-defined var 13

@property uservar14 type=classificator field=aw_varuser10 reltype=RELTYPE_VARUSER84 store=connect
@caption User-defined var 14

@property uservar15 type=classificator field=aw_varuser10 reltype=RELTYPE_VARUSER85 store=connect
@caption User-defined var 15

@property uservar16 type=classificator field=aw_varuser10 reltype=RELTYPE_VARUSER86 store=connect
@caption User-defined var 16

@property userdate1 type=date_select field=aw_userdate1 year_from=1970 year_to=2020
@caption User-defined date select 1

@property userdate2 type=date_select field=aw_userdate2 year_from=1970 year_to=2020
@caption User-defined date select 2

@property userdate3 type=date_select field=aw_userdate3 year_from=1970 year_to=2020
@caption User-defined date select 3

@property userdate4 type=date_select field=aw_userdate4 year_from=1970 year_to=2020
@caption User-defined date select 4

@property userdate5 type=date_select field=aw_userdate5 year_from=1970 year_to=2020
@caption User-defined date select 5

@property userdate6 type=date_select field=aw_userdate6 year_from=1970 year_to=2020
@caption User-defined date select 6

@property userdate7 type=date_select field=aw_userdate7 year_from=1970 year_to=2020
@caption User-defined date select 7

@property userdate8 type=date_select field=aw_userdate8 year_from=1970 year_to=2020
@caption User-defined date select 8

@property userdate9 type=date_select field=aw_userdate9 year_from=1970 year_to=2020
@caption User-defined date select 9

@property userdate10 type=date_select field=aw_userdate10 year_from=1970 year_to=2020
@caption User-defined date select 10

@property userdate11 type=date_select field=aw_userdate11 year_from=1970 year_to=2020
@caption User-defined date select 11

@property userdate12 type=date_select field=aw_userdate12 year_from=1970 year_to=2020
@caption User-defined date select 12

@property userdate13 type=date_select field=aw_userdate13 year_from=1970 year_to=2020
@caption User-defined date select 13

@property userdate14 type=date_select field=aw_userdate14 year_from=1970 year_to=2020
@caption User-defined date select 14

@property userdate15 type=date_select field=aw_userdate15 year_from=1970 year_to=2020
@caption User-defined date select 15

@property userch1 type=checkbox field=aw_userch1 ch_value=1 datatype=int
@caption User-defined checkbox 1

@property userch2 type=checkbox field=aw_userch2 ch_value=1 datatype=int
@caption User-defined checkbox 2

@property userch3 type=checkbox field=aw_userch3 ch_value=1 datatype=int
@caption User-defined checkbox 3

@property userch4 type=checkbox field=aw_userch4 ch_value=1 datatype=int
@caption User-defined checkbox 4

@property userch5 type=checkbox field=aw_userch5 ch_value=1 datatype=int
@caption User-defined checkbox 5

@property userch6nostore type=checkbox store=no ch_value=1
@caption User-defined checkbox 6 (store=no)

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

@property usertext1 type=text store=no
@caption Usertext1

@property usertext2 type=text store=no
@caption Usertext2

@property usertext3 type=text store=no
@caption Usertext3

@property usertext4 type=text store=no
@caption Usertext4

@property usertext5 type=text store=no
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

@default table=objects
@default method=serialize
@default field=meta

@property usersel1 type=select
@caption User-defined select 1

@property usersel2 type=select
@caption User-defined select 2

@property usersel3 type=select
@caption User-defined select 3

@property usersel4 type=select
@caption User-defined select 4

@property usersel5 type=select
@caption User-defined select 5


@property udefhidden1 type=hidden
@property udefhidden2 type=hidden
@property udefhidden3 type=hidden

---------------- textbox ----------------

@groupinfo textboxes caption="Textbox"
@default group=textboxes

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

@property user51 type=textbox
@caption User-defined 51

@property user52 type=textbox
@caption User-defined 52

@property user53 type=textbox
@caption User-defined 53

@property user54 type=textbox
@caption User-defined 54

@property user55 type=textbox
@caption User-defined 55

@property user56 type=textbox
@caption User-defined 56

@property user57 type=textbox
@caption User-defined 57

@property user58 type=textbox
@caption User-defined 58

@property user59 type=textbox
@caption User-defined 59

@property user60 type=textbox
@caption User-defined 60

@property user61 type=textbox
@caption User-defined 61

@property user62 type=textbox
@caption User-defined 62

@property user63 type=textbox
@caption User-defined 63

@property user64 type=textbox
@caption User-defined 64

@property user65 type=textbox
@caption User-defined 65

@property user66 type=textbox
@caption User-defined 66

@property user67 type=textbox
@caption User-defined 67

@property user68 type=textbox
@caption User-defined 68

@property user69 type=textbox
@caption User-defined 69

@property user70 type=textbox
@caption User-defined 70

---------------- textarea ----------------

@groupinfo textareas caption="Textarea"
@default group=textareas

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

@property uservar56 type=classificator reltype=RELTYPE_VARUSER56 store=connect
@caption User-defined var 56

@property uservar57 type=classificator reltype=RELTYPE_VARUSER57 store=connect
@caption User-defined var 57

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

@property uservar81 type=classificator reltype=RELTYPE_VARUSER86 store=connect
@caption User-defined var 81

@property uservar82 type=classificator reltype=RELTYPE_VARUSER87 store=connect
@caption User-defined var 82

@property uservar83 type=classificator reltype=RELTYPE_VARUSER88 store=connect
@caption User-defined var 83

@property uservar84 type=classificator reltype=RELTYPE_VARUSER89 store=connect
@caption User-defined var 84

@property uservar85 type=classificator reltype=RELTYPE_VARUSER90 store=connect
@caption User-defined var 85

@property uservar86 type=classificator reltype=RELTYPE_VARUSER91 store=connect
@caption User-defined var 86

@property uservar87 type=classificator reltype=RELTYPE_VARUSER92 store=connect
@caption User-defined var 87

@property uservar88 type=classificator reltype=RELTYPE_VARUSER93 store=connect
@caption User-defined var 88

@property uservar89 type=classificator reltype=RELTYPE_VARUSER94 store=connect
@caption User-defined var 89

@property uservar90 type=classificator reltype=RELTYPE_VARUSER95 store=connect
@caption User-defined var 90

@property uservar91 type=classificator reltype=RELTYPE_VARUSER96 store=connect
@caption User-defined var 91

@property uservar92 type=classificator reltype=RELTYPE_VARUSER97 store=connect
@caption User-defined var 92

@property uservar93 type=classificator reltype=RELTYPE_VARUSER98 store=connect
@caption User-defined var 93

@property uservar94 type=classificator reltype=RELTYPE_VARUSER99 store=connect
@caption User-defined var 94

@property uservar95 type=classificator reltype=RELTYPE_VARUSER100 store=connect
@caption User-defined var 95

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

---------------- pildiupload -----------

@property userim1 type=releditor reltype=RELTYPE_IMAGE1 rel_id=first use_form=emb field=meta method=serialize
@caption Pildiupload 1

@property userim2 type=releditor reltype=RELTYPE_IMAGE2 rel_id=first use_form=emb field=meta method=serialize
@caption Pildiupload 2

@property userim3 type=releditor reltype=RELTYPE_IMAGE3 rel_id=first use_form=emb field=meta method=serialize
@caption Pildiupload 3

@property userim4 type=releditor reltype=RELTYPE_IMAGE4 rel_id=first use_form=emb field=meta method=serialize
@caption Pildiupload 4

@property userim5 type=releditor reltype=RELTYPE_IMAGE5 rel_id=first use_form=emb field=meta method=serialize
@caption Pildiupload 5

@property userim6 type=releditor reltype=RELTYPE_IMAGE6 rel_id=first use_form=emb field=meta method=serialize
@caption Pildiupload 6

@property userim7 type=releditor reltype=RELTYPE_IMAGE7 rel_id=first use_form=emb field=meta method=serialize
@caption Pildiupload 7

@property userim8 type=releditor reltype=RELTYPE_IMAGE8 rel_id=first use_form=emb field=meta method=serialize
@caption Pildiupload 8

@property userim9 type=releditor reltype=RELTYPE_IMAGE9 rel_id=first use_form=emb field=meta method=serialize
@caption Pildiupload 9

@property userim10 type=releditor reltype=RELTYPE_IMAGE10 rel_id=first use_form=emb field=meta method=serialize
@caption Pildiupload 10

@property userim11 type=releditor reltype=RELTYPE_IMAGE11 rel_id=first use_form=emb field=meta method=serialize
@caption Pildiupload 11

@property userim12 type=releditor reltype=RELTYPE_IMAGE12 rel_id=first use_form=emb field=meta method=serialize
@caption Pildiupload 12

@property userim13 type=releditor reltype=RELTYPE_IMAGE13 rel_id=first use_form=emb field=meta method=serialize
@caption Pildiupload 13

@property userim14 type=releditor reltype=RELTYPE_IMAGE14 rel_id=first use_form=emb field=meta method=serialize
@caption Pildiupload 14

@property userim15 type=releditor reltype=RELTYPE_IMAGE15 rel_id=first use_form=emb field=meta method=serialize
@caption Pildiupload 15

@property userim16 type=releditor reltype=RELTYPE_IMAGE16 rel_id=first use_form=emb field=meta method=serialize
@caption Pildiupload 16

@property userim17 type=releditor reltype=RELTYPE_IMAGE17 rel_id=first use_form=emb field=meta method=serialize
@caption Pildiupload 17

@property userim18 type=releditor reltype=RELTYPE_IMAGE18 rel_id=first use_form=emb field=meta method=serialize
@caption Pildiupload 18

@property userim19 type=releditor reltype=RELTYPE_IMAGE19 rel_id=first use_form=emb field=meta method=serialize
@caption Pildiupload 19

@property userim20 type=releditor reltype=RELTYPE_IMAGE20 rel_id=first use_form=emb field=meta method=serialize
@caption Pildiupload 20

@property userim21 type=releditor reltype=RELTYPE_IMAGE21 rel_id=first use_form=emb field=meta method=serialize
@caption Pildiupload 21

@property userim22 type=releditor reltype=RELTYPE_IMAGE22 rel_id=first use_form=emb field=meta method=serialize
@caption Pildiupload 22

@property userim23 type=releditor reltype=RELTYPE_IMAGE23 rel_id=first use_form=emb field=meta method=serialize
@caption Pildiupload 23

@property userim24 type=releditor reltype=RELTYPE_IMAGE24 rel_id=first use_form=emb field=meta method=serialize
@caption Pildiupload 24

@property userim25 type=releditor reltype=RELTYPE_IMAGE25 rel_id=first use_form=emb field=meta method=serialize
@caption Pildiupload 25

@property userim26 type=releditor reltype=RELTYPE_IMAGE26 rel_id=first use_form=emb field=meta method=serialize
@caption Pildiupload 26

@property userim27 type=releditor reltype=RELTYPE_IMAGE27 rel_id=first use_form=emb field=meta method=serialize
@caption Pildiupload 27

@property userim28 type=releditor reltype=RELTYPE_IMAGE28 rel_id=first use_form=emb field=meta method=serialize
@caption Pildiupload 28

@property userim29 type=releditor reltype=RELTYPE_IMAGE29 rel_id=first use_form=emb field=meta method=serialize
@caption Pildiupload 29

@property userim30 type=releditor reltype=RELTYPE_IMAGE30 rel_id=first use_form=emb field=meta method=serialize
@caption Pildiupload 30

--------------- failiupload ------------

@property userfile1 type=releditor reltype=RELTYPE_FILE1 rel_id=first use_form=emb field=meta method=serialize
@caption Failiupload 1

@property userfile2 type=releditor reltype=RELTYPE_FILE2 rel_id=first use_form=emb field=meta method=serialize
@caption Failiupload 2

@property userfile3 type=releditor reltype=RELTYPE_FILE3 rel_id=first use_form=emb field=meta method=serialize
@caption Failiupload 3

@property userfile4 type=releditor reltype=RELTYPE_FILE4 rel_id=first use_form=emb field=meta method=serialize
@caption Failiupload 4

@property userfile5 type=releditor reltype=RELTYPE_FILE5 rel_id=first use_form=emb field=meta method=serialize
@caption Failiupload 5

---------------- seosed ----------------

@reltype VARUSER1 value=1 clid=CL_META
@caption kasutajadefineeritud muutuja 1

@reltype VARUSER2 value=2 clid=CL_META
@caption kasutajadefineeritud muutuja 2

@reltype VARUSER3 value=3 clid=CL_META
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

@reltype VARUSER10 value=100 clid=CL_META
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

@reltype VARUSER81 value=81 clid=CL_META
@caption kasutajadefineeritud muutuja 81

@reltype VARUSER82 value=82 clid=CL_META
@caption kasutajadefineeritud muutuja 82

@reltype VARUSER83 value=83 clid=CL_META
@caption kasutajadefineeritud muutuja 83

@reltype VARUSER84 value=84 clid=CL_META
@caption kasutajadefineeritud muutuja 84

@reltype VARUSER85 value=85 clid=CL_META
@caption kasutajadefineeritud muutuja 85

@reltype VARUSER86 value=86 clid=CL_META
@caption kasutajadefineeritud muutuja 86

@reltype VARUSER87 value=87 clid=CL_META
@caption kasutajadefineeritud muutuja 87

@reltype VARUSER88 value=88 clid=CL_META
@caption kasutajadefineeritud muutuja 88

@reltype VARUSER89 value=89 clid=CL_META
@caption kasutajadefineeritud muutuja 89

@reltype VARUSER90 value=90 clid=CL_META
@caption kasutajadefineeritud muutuja 90

@reltype VARUSER91 value=111 clid=CL_META
@caption kasutajadefineeritud muutuja 91

@reltype VARUSER92 value=112 clid=CL_META
@caption kasutajadefineeritud muutuja 92

@reltype VARUSER93 value=113 clid=CL_META
@caption kasutajadefineeritud muutuja 93

@reltype VARUSER94 value=114 clid=CL_META
@caption kasutajadefineeritud muutuja 94

@reltype VARUSER95 value=115 clid=CL_META
@caption kasutajadefineeritud muutuja 95

@reltype VARUSER96 value=116 clid=CL_META
@caption kasutajadefineeritud muutuja 96

@reltype VARUSER97 value=117 clid=CL_META
@caption kasutajadefineeritud muutuja 97

@reltype VARUSER98 value=118 clid=CL_META
@caption kasutajadefineeritud muutuja 98

@reltype VARUSER99 value=119 clid=CL_META
@caption kasutajadefineeritud muutuja 909

@reltype VARUSER100 value=120 clid=CL_META
@caption kasutajadefineeritud muutuja 100


@reltype REGISTER value=10 clid=CL_REGISTER
@caption Seostatud register

@reltype IMAGE1 value=81 clid=CL_IMAGE
@caption pilt1

@reltype IMAGE2 value=82 clid=CL_IMAGE
@caption pilt2

@reltype IMAGE3 value=83 clid=CL_IMAGE
@caption pilt3

@reltype IMAGE4 value=84 clid=CL_IMAGE
@caption pilt4

@reltype IMAGE5 value=85 clid=CL_IMAGE
@caption pilt5

@reltype IMAGE6 value=86 clid=CL_IMAGE
@caption pilt6

@reltype IMAGE7 value=87 clid=CL_IMAGE
@caption pilt7

@reltype IMAGE8 value=88 clid=CL_IMAGE
@caption pilt8

@reltype IMAGE9 value=89 clid=CL_IMAGE
@caption pilt9

@reltype IMAGE10 value=90 clid=CL_IMAGE
@caption pilt10

@reltype IMAGE11 value=91 clid=CL_IMAGE
@caption pilt11

@reltype IMAGE12 value=92 clid=CL_IMAGE
@caption pilt12

@reltype IMAGE13 value=93 clid=CL_IMAGE
@caption pilt13

@reltype IMAGE14 value=94 clid=CL_IMAGE
@caption pilt14

@reltype IMAGE15 value=95 clid=CL_IMAGE
@caption pilt15

@reltype IMAGE16 value=96 clid=CL_IMAGE
@caption pilt16

@reltype IMAGE17 value=97 clid=CL_IMAGE
@caption pilt17

@reltype IMAGE18 value=98 clid=CL_IMAGE
@caption pilt18

@reltype IMAGE19 value=99 clid=CL_IMAGE
@caption pilt19

@reltype IMAGE20 value=100 clid=CL_IMAGE
@caption pilt20

@reltype IMAGE21 value=101 clid=CL_IMAGE
@caption pilt21

@reltype IMAGE22 value=102 clid=CL_IMAGE
@caption pilt22

@reltype IMAGE23 value=103 clid=CL_IMAGE
@caption pilt23

@reltype IMAGE24 value=104 clid=CL_IMAGE
@caption pilt24

@reltype IMAGE25 value=105 clid=CL_IMAGE
@caption pilt25

@reltype IMAGE26 value=106 clid=CL_IMAGE
@caption pilt26

@reltype IMAGE27 value=107 clid=CL_IMAGE
@caption pilt27

@reltype IMAGE28 value=108 clid=CL_IMAGE
@caption pilt28

@reltype IMAGE29 value=109 clid=CL_IMAGE
@caption pilt29

@reltype IMAGE30 value=110 clid=CL_IMAGE
@caption pilt30

@reltype IMAGE5 value=85 clid=CL_IMAGE
@caption pilt5

@reltype FILE1 value=86 clid=CL_FILE
@caption fail1

@reltype FILE2 value=87 clid=CL_FILE
@caption fail2

@reltype FILE3 value=88 clid=CL_FILE
@caption fail3

@reltype FILE4 value=89 clid=CL_FILE
@caption fail4

@reltype FILE5 value=90 clid=CL_FILE
@caption fail5


@property aliasmgr type=aliasmgr store=no editonly=1 group=data,classificators,textboxes,textareas,texts trans=1
@caption Aliastehaldur

*/

class register_data extends class_base
{
	function register_data()
	{
		$this->init(array(
			"tpldir" => "applications/register/register_data",
			"clid" => CL_REGISTER_DATA
		));
	}

	function callback_pre_edit($arr)
	{
		if($register = $arr["obj_inst"]->get_first_obj_by_reltype("RELTYPE_REGISTER"))
		{
			$default = safe_array($register->prop("gfdg"));
			if($register->prop("default_cfgform") == 1 && !empty($default[0]))
			{
				$arr["obj_inst"]->set_meta("cfgform_id", $default[0]);
			}
		}
	}

	function get_property($arr)
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
		};
		if ($arr["new"])
		{
			$this->set_register_id = $arr["request"]["set_register_id"];
			$cfg_id = $arr["request"]["cfgform"];
		}
		else
		{
			$this->set_register_id = $arr["obj_inst"]->prop("register_id");
			$cfg_id = $arr["obj_inst"]->meta("cfgform_id");
		};
		if ($this->set_register_id && $cfg_id)
		{
			$rego = obj($this->set_register_id);
			if ($rego->prop("cfgform_name_in_field") != "" && $prop["name"] == $rego->prop("cfgform_name_in_field"))
			{
				$co = obj($cfg_id);
				$prop["value"] = $co->name();
			}
		}
		return $retval;
	}

	function set_property($arr = array())
	{
		$prop = &$arr["prop"];
		$retval = PROP_OK;
		switch($prop["name"])
		{
		}

		return $retval;
	}

	function callback_mod_reforb($arr)
	{
		$arr["set_register_id"] = $this->set_register_id;
	}

	function callback_pre_save($arr)
	{
		if ($arr["new"] && $arr["request"]["set_register_id"])
		{
			$arr["obj_inst"]->set_prop("register_id", $arr["request"]["set_register_id"]);
			if ($arr["request"]["cfgform"])
			{
				$rego = obj($arr["request"]["set_register_id"]);
				if ($rego->prop("cfgform_name_in_field") != "")
				{
					$co = obj($arr["request"]["cfgform"]);
					$arr["obj_inst"]->set_prop($rego->prop("cfgform_name_in_field"), $co->name());
				}
			}
		}


		// if there is a register attached, then see if that has a webform
		// and then do the rename thingie
		if ($this->can("view", $arr["obj_inst"]->prop("register_id")))
		{
			$ro = obj($arr["obj_inst"]->prop("register_id"));
			$conns = $ro->connections_to(array("from.class_id" => CL_WEBFORM));
			if (count($conns))
			{
				$c = reset($conns);
				$wf = $c->from();

				$name = "";
				$prplist = $arr["obj_inst"]->get_property_list();
				foreach(safe_array($wf->prop("obj_name")) as $key => $val)
				{
					if ($prplist[$key]["type"] == "date_select")
					{
						if ($arr["obj_inst"]->prop($key)  != -1)
						{
							$name .= " ".date("d.m.Y", $arr["obj_inst"]->prop($key));
						}
					}
					else
					if ($prplist[$key]["type"] == "datetime_select")
					{
						if ($arr["obj_inst"]->prop($key)  != -1)
						{
							$name .= " ".date("d.m.Y H:i", $arr["obj_inst"]->prop($key));
						}
					}
					else
					{
						$name .= " ".$arr["obj_inst"]->prop_str($key); //$arr[$key];
					}
				}
				$arr["obj_inst"]->set_name(trim($name));
			}
		}
	}

	function callback_post_save($arr)
	{
		if($arr['new'] && $arr['request']['cfgform'])
		{
			$tmp_cfgform = obj($arr['request']['cfgform']);
			$conns = $tmp_cfgform->connections_to(array("from.class_id" => CL_REGISTER));
			$register_obj = $conns[0]->from();

			$mail_addr_to = $register_obj->prop("mail_address_to");

			$mail_addresses_to = "";
			$mail_addresses = new aw_array($mail_addr_to);
			foreach($mail_addresses->get() as $address)
			{
				if(is_oid($address))
				{
					$tmp = obj($address);
					$mail_addresses_to .= $tmp->prop("mail").", ";
				}
			}


			if(!empty($mail_addresses_to))
			{

				$mail_addr_from = $register_obj->prop("mail_address_from");
				if (empty($mail_addr_from))
				{
					if (aw_global_get("uid") != "")
					{
						$u = obj(aw_global_get("uid_oid"));
						$mail_addr_from = $u->prop("email");
					}
				}
				else
				{
					if(is_oid($mail_addr_from))
					{
						$mail_addr_from = obj($mail_addr_from);
						$mail_addr_from = $mail_addr_from->prop("mail");
					}
				}
				$mail_subj = $register_obj->prop("mail_subject");
				$headers = "To: ".$mail_addresses_to."\r\n";
				$headers .= "From: ".$mail_addr_from."\r\n";
				$url = $this->mk_my_orb("change", array("id" => $arr['id']));
				$mail_addresses_to = substr($mail_addresses_to, 0, (strlen($mail_addresses_to)-2));
/*
				arr("address_to: ".$mail_addresses_to);
				arr("addr_to ".$mail_addr_to);
				arr("addr_from ".$mail_addr_from);
				arr("subj ".$mail_subj);
				arr("headers ".$headers);
*/
				send_mail($mail_addresses_to, $mail_subj, $url, $headers);
			}
		}

	}

	function callback_mod_retval($arr)
	{
		// if there is some address set in register obj. where the user should be redirected, then
		// lets do it
		if (!empty($arr['request']['set_register_id']))
		{
			$register = obj($arr['request']['set_register_id']);
			$data_return_url = $register->prop("data_return_url");
			if (!empty($data_return_url))
			{
				$arr['args']['goto'] = aw_ini_get("baseurl")."/".$register->prop("data_return_url");
			}
		}
	}
	function parse_alias($arr)
	{
		return $this->show(array("id" => $arr["alias"]["target"]));
	}

	////
	// !shows the data
	function show($arr)
	{
		$ot = get_instance(CL_OBJECT_TYPE);
		$cf = get_instance(CL_CFGFORM);
		$obj_inst = obj($arr["id"]);
		$ot_id = $ot->get_obj_for_class(array(
			"clid" => $obj_inst->class_id(),
			"general" => true,
		));
		$return_url = aw_global_get("REQUEST_URI");
		if($register = $obj_inst->get_first_obj_by_reltype("RELTYPE_REGISTER"))
		{
			$url = $register->prop("data_return_url");
			if(!empty($url))
			{
				$return_url = $url;
			}
		}

		return $cf->draw_cfgform_from_ot(array(
			"ot" => $ot_id,
			"reforb" => $this->mk_reforb("save_form_data", array(
				"id" => $arr["id"],
				"return_url" => $return_url,
			)),
		));
	}

	/**

		@attrib name=save_form_data nologin=1 all_args=1

		@param id required type=int acl=view
		@param return_url optional
	**/
	function save_form_data($arr)
	{
		$rval = aw_ini_get("baseurl").$arr["return_url"];
		$obj_inst = obj($arr["id"]);
		$ot = get_instance(CL_OBJECT_TYPE);
		if(!$ot_id = $ot->get_obj_for_class(array(
			"clid" => $obj_inst->class_id(),
			"general" => true,
		)))
		{
			return $rval;
		}
		$ot = obj($ot_id);

		$is_valid = $this->validate_data(array(
			"cfgform_id" => $ot->prop("use_cfgform"),
			"request" => $arr,
		));
		if(empty($is_valid))
		{
			$o = obj();
			$parent = $obj_inst->parent();
			if($register = $obj_inst->get_first_obj_by_reltype("RELTYPE_REGISTER"))
			{
				$prop = $register->prop("data_rootmenu");
				if(!empty($prop))
				{
					$parent = $prop;
				}
			}
			$o->set_class_id(CL_REGISTER_DATA);
			$o->set_parent($parent);
			foreach($o->get_property_list() as $pn => $pd)
			{
				$o->set_prop($pn, $arr[$pn]);
			}
			$o->set_meta("object_type", $ot->id());
			$o->set_meta("cfgform_id", $ot->prop("use_cfgform"));
			$o->set_name("entry ".date("d-m-Y H:i"));
			$o->save();
		}
		return $rval;
	}

	/**

		@attrib name=view nologin=1 all_args=1

	**/
	function view($arr)
	{
		return parent::view($arr);
	}

	function do_db_upgrade($t, $f)
	{
		switch($f)
		{
			case "aw_userdate6":
			case "aw_userdate7":
			case "aw_userdate8":
			case "aw_userdate9":
			case "aw_userdate10":
			case "aw_userdate11":
			case "aw_userdate12":
			case "aw_userdate13":
			case "aw_userdate14":
			case "aw_userdate15":
				$this->db_add_col($t, array(
					"name" => $f,
					"type" => "int"
				));
				return true;
		}
	}
}
