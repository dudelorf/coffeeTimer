create table coffeeStuff
(methodName char(50) not null,
 defaultVolume int unsigned not null,
 brewRatio float(2,1) not null,
 phases char(70) not null,
 bloomRatio float(2,1) not null);
 
 insert into coffeeStuff
 set methodName = "Aeropress",
	 defaultVolume = 8,
	 brewRatio = 18.0,
	 phases = "[30, 60, 15]",
	 bloomRatio = 2.5;