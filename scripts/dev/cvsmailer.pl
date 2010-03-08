#!/usr/bin/perl

my ($op, $mod, $rin, $file);

my $cvsweb = "http://test.kirjastus.ee/cgi-bin/cvsweb";

$op = $ARGV[0];

@ARGV = split(' ', $ARGV[0]); # all in the first argument
$mod = shift; # module name

if ($op =~ /- Imported sources$/) {
	$op = 'import';
	$file = "$ENV{USER} imported files into module '$mod'."
}
elsif ($op =~ /- Imported directory$/)
{
	$op = 'commit';
	$file = "$ENV{USER} added a directory into module '$mod'.";
} else {

	$op = 'commit';
	
	my ($oldvers, $newvers, @files);
	while (($file, $oldvers, $newvers) = split(',', shift)) {
		$oldvers = '<added>'   if $oldvers eq 'NONE';
	 	$newvers = '<deleted>' if $newvers eq 'NONE';
		if ($oldvers eq '<added>')
		{
			push @files, " $cvsweb/cvsweb/$mod/$file [added]";
		}
		elsif ($op =~ /- New directory$/)
		{
			push @files, " $cvsweb/cvsweb/$mod/$file [added directory]";
		}
		elsif ($newvers eq '<deleted>')
		{
			push @files, " $cvsweb/cvsweb/$mod/$file [removed]";
		}
		else
		{
			push @files, " $cvsweb/cvsweb/$mod/$file [$oldvers -> $newvers]";
		};
		if ( ($oldvers ne '<added>') && ($newvers ne '<deleted>') )
		{
			push @files, " D: $cvsweb/$mod/$file.diff?r1=$oldvers&r2=$newvers&f=h";
		};
	}

	$file = "In module '$mod', $ENV{USER} committed:\n\n" . join("\n", @files);
}

open (MSG, "|/usr/sbin/sendmail cvs\@struktuur.ee"); ## CHANGE ME!
#open (MSG, ">blah.txt");

my ($indir, $modfiles, $logmsg, $cvsroot);

$cvsroot = $ENV{CVSROOT};
$cvsroot =~ s|/$||;

HEADER: while (<>) {
	last HEADER if /^$/;
	$mod = $1,    next HEADER if ?^Update of $cvsroot/(.*)?o;
  $indir = $1,  next HEADER if ?^In directory (.*)?;
}

my (@modfiles, $filelist, $repl);

if ($op eq 'commit') {

	MODFILE: while (<>) {
		last MODFILE if /^Log/;
		if (/^(\w+)/) {
			if    ($1 eq 'Modified') { $repl = undef; }
			elsif ($1 eq 'Removed')  { $repl = '"($1)"'; }
			elsif ($1 eq 'Added')    { $repl = '"+$1"'; }
			next MODFILE;
		}
		s/^\t//;
		chomp;
		s/ $//;
		s/^(.*)$/$repl/ee if $repl;
		push @modfiles, $_;
	}

	$filelist = ': ' . join(', ', @modfiles);

} else { # got a log message instead
	$_ = <>;
}

print MSG qq|From: CVS log mailer <dev\@struktuur.ee>
To: CVS commits <cvs\@struktuur.ee>
Subject: CVS $op by $ENV{USER} in $mod$filelist
Reply-To: <cvs\@struktuur.ee>

$file

Log message:

|;

while (<>) {
	print MSG $_;
}

close MSG;
