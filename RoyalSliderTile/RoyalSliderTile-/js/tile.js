		TileBuilder=function(){
			if(arguments.length==0) return;
			this.tilesPane=document.getElementById(arguments[0]);
		this.h=[];//水平条位置
		this.v=[];//垂直条位置
		this.su=150;//定义标准块150px × 150px;
		this.space=2;//定义块间距，上下左右相同
		this.mode=8;//6,8,12 tilepane内全部排标准块的块数
		this.row=2;
		this.s0=[];//标准布局[1,2,3,4,5,6,7,8]
		this.s1=[];//长条占位排除列表
		this.s=[];//标准块位置		
		this.ps=[];//tile position and size like p[0]=[top,left,width,height];
		this.colors=['00BEE3','E4A218','1BAE5D','66C010','C51A3B',
				'DEC801','CBF00A','86936D','30445C','232523','222E3F','A37551','E80A0A','0466CA','FFDE00','76C55E'];
		this.imgs=[];
		this.urls=[];
		if(arguments.length>1){
			params=arguments[1];
			if(params.mode)this.mode=params.mode;
			if(params.row)this.row=params.row;
			if(params.h)this.h=params.h;
			if(params.v)this.v=params.v;
			if(params.su)this.su=params.su;
			if(params.space)this.space=params.space;
			if(params.colors)this.colors=params.colors;
			if(params.imgs)this.imgs=params.imgs;
			if(params.urls)this.urls=params.urls;
		}
		var mode=this.mode+1;
		for(i=1;i<mode;i++){
			this.s0.push(i);
		}
		var col=Math.ceil(this.mode/this.row);
		var step=this.su+this.space;
		var k=0;
		var t=0,l=0,width=0;height=0;
		//计算水平条位置和大小
		width=this.su*2+this.space;height=this.su;
		for(i=0;i<this.h.length;i++) {   
			var n=this.h[i];
	     this.s1.push(n);   
	     this.s1.push(n+1);  
//	     if(n<col){
//	     	t=0;
//	     	l=(n-1)*step;
//	     }else{
	    	t=Math.floor((n-1)/col)*step;
	     	l=((n-1)%col)*step;
//	     }	     
	     this.ps.push([t,l,width,height]);
		}
		//计算垂直条位置和大小
		width=this.su;height=this.su*2+this.space;
		for(i=0;i<this.v.length;i++) {   
			var n=this.v[i];
	     this.s1.push(n);   
	     this.s1.push(n+col);  
	     t=Math.floor((n-1)/col)*step;
	     l=((n-1)%col)*step;
	     
	     this.ps.push([t,l,width,height]);
		}
		//document.write("s1= "+this.s1);
		for(i=0;i<this.s0.length;i++) { 
			var without=false;			
			for(j=0;j<this.s1.length;j++) { 
				if(this.s0[i]==this.s1[j]) {without=true;this.s1.splice(j,1);break;}
			}
			if(!without)this.s.push(this.s0[i]);
		}
		//计算标准块位置和大小
		width=this.su;height=this.su;
		for(i=0;i<this.s.length;i++) {   
			var n=this.s[i];
		     t=Math.floor((n-1)/col)*step;
	     	 l=((n-1)%col)*step;
	     this.ps.push([t,l,width,height]);
		}
		
//		document.write("<hr>");
//		document.write("s= "+this.s);
//		document.write("<hr>");
//		document.write(this.ps);
		this.build=function(){
			//var tilesPane=document.getElementById("tilesPane");
			var tiles="";
			for(i=0;i<this.ps.length;i++){			
				tiles+="<div style='color:white;background:url("+this.imgs[i]+") #"+this.colors[i]+";border:0px solid white;position:absolute;"+setPosition(this.ps[i])+"'>"+(i+1);
				tiles+="<div class='txt_holder'><div class='caption_bg'></div>";
				tiles+="<div class='caption_txt'>Captions Here<br>Subtitle</div></div>";				
				tiles+="</div>";
			}
			this.tilesPane.innerHTML=tiles;
			this.tilesPane.style.width=(step*col-this.space)+"px";
			this.tilesPane.style.height=(step*this.row-this.space)+"px";
			var tilesDom=this.tilesPane.children;
			for(i=0;i<this.urls.length;i++){		
				if(this.urls[i]){	//alert(this.urls[i]);
					var info={html:this.urls[i],tile:tilesDom[i]};
					loadSlider(info);
				}
			}
			
		};
	}
		
		function setPosition(arr){
			return "top:"+arr[0]+"px;left:"+arr[1]+"px;width:"+arr[2]+"px;height:"+arr[3]+"px;";
		}
		
		function loadSlider(info) {                
            	ajaxRequest = $.ajax({
            		url: info.html,
            		cache: false,
            		success: function(html){ 
            			ajaxRequest = null;
            			h=info.tile.innerHTML;
            			info.tile.innerHTML="";
            			$(info.tile).append(html);//"<div>Something</div><script>alert('Hello');</script>";  
            			//$(info.tile).append(h);    			
            		}
            	});            	
		    }	