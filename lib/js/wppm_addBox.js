var count = 1;
function expandpbox()
{
	document.getElementById("test"+count).style.display = 'block';
	var count2 = count + 1 ;
	var ifrmID = 'wppmeditor'+count2+'_ifr';
	document.getElementById(ifrmID).style.height = '300px' ;
	count++;
	if( count == 15 )
	{
		document.getElementById("expandBox").style.display = 'none' ;
	}
}
