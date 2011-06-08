function resize(){
	if(myDataTable!=null){
		numColumnas=myDataTable.getColumnSet().getDefinitions().length;
		var num=0;
		var width=(document.getElementById("dcuerpo").clientWidth)-160;
		
		for (i=0;i<numColumnas;i++){
			var column=myDataTable.getColumn(i);
			if (!column.hidden){
				num++;
			}
		}
		num-=2;
		var c=0
		width-=(num*25)
		for (i=0;i<numColumnas;i++){
			var column=myDataTable.getColumn(i);
			if (!column.hidden && c<num){
				myDataTable.setColumnWidth(column,(width/num));
				c++;
			}
		}
		
	}
}