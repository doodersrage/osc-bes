#!/usr/bin/perl
use DBI;
$|=1;
my $redirect_server="http://www.boatequipmentsuperstore.com"; #or dev site
my $database="bes_osc";      #your database
my $hostname="localhost"; #probably localhost (or a central MySQL server)
my $port=3306;
my $user="bes_dbadmin"; #db user
my $pass="bespass"; #password for the user
my $htaccessfile="../.htaccess";
my $delim_begin="#BEGIN STATIC MAPPING";
my $delim_end="#END STATIC MAPPING";
my $ht_contents="";
my $redir_contents="";

print <<HEADER;
Content-type: text/html

<HTML>
<HEAD><TITLE>UPDATE LISTING</TITLE></HEAD>
<BODY>
HEADER

 my $dsn = "DBI:mysql:database=$database;host=$hostname;port=$port"; 
my $dbh=DBI->connect($dsn,$user,$pass) ;
if (! $dbh)
{
        print "Connection failed <br>\n";
        print "</body></html>";
exit;
}

my $sth=$dbh->prepare("select * from seo_urls");
$sth->execute() or die 'unable to execute\n';


if (-f $htaccessfile)
{
open (FH, $htaccessfile) or die 'unable to open .htaccess';
while (<FH>)
{
$ht_contents .= $_;
}
close FH;
}  
if (  $ht_contents !~ /$delim_begin(.*)$delim_end/s )
{
        $ht_contents.=$delim_begin."\n".$delim_end."\n";
}
$redir_contents=$delim_begin."\n"."RewriteEngine on\n";       

while (my $ref=$sth->fetchrow_hashref())
{
if ($ref->{'surls_name'} =~ /^[A-Za-z0-9\-]+$/) { 
if ($ref->{'surls_script'} ne 'product_info.php')
{
        $redir_contents .= "RewriteRule ^". $ref->{'surls_name'} . "/(.+)-(.+)-(.+)\.php catalog/\$1.php?".$ref->{'surls_param'}."&page=\$2&sort=\$3 [QSA,NS,L]\n";
        $redir_contents .= "RewriteRule ^". $ref->{'surls_name'} . "/(.+)-(.+)\.php catalog/\$1.php?".$ref->{'surls_param'}."&page=\$2 [QSA,NS,L]\n";

                $redir_contents .= "RewriteRule ^". $ref->{'surls_name'} . "/(.+) catalog/\$1?" . $ref->{'surls_param'} ." [QSA,NS,L]\n";
}
                $redir_contents .= "RewriteRule ^". $ref->{'surls_name'} . "/ catalog/".$ref->{'surls_script'}."?".$ref->{'surls_param'}." [QSA,NS,L]\n";
} else
     {
                print "Not adding '".$ref->{'surls_name'}."' script=".$ref->{'surls_script'}." param=".$ref->{'surls_param'}." <br>\n";
        }     
}
$redir_contents.=$delim_end."\n";
$dbh->disconnect;
$ht_contents =~ s/$delim_begin(.*)$delim_end/$redir_contents/s;
open (FH, "> ".$htaccessfile) or die 'still can not write';
print FH  $ht_contents;
close FH;
print "SUCCESS\n";
print "</body></html>";

exit 0;             
