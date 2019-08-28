
var wsServer = 'ws://192.168.75.135:9501'; 
var ws = new WebSocket(wsServer);

ws.onopen = function (evt) { 
	var params = GetRequest();
	var username = params['username'];
	var userInfo = '{"type":"1","test":"33","data":"' + username + '"}';
	ws.send(userInfo);
}; 
ws.onmessage = function (evt) { 
	var data = JSON.parse(evt.data);
	if (data.type == 1) {
		appendUserList(data);
		changeTalk();
	}else if (data.type == 1) {
		appendUserMsg(data);
	}
};
ws.onclose = function() {
	console.log('断开连接')
};

function sendMsg(e){
	var params = GetRequest();
	var from = params['username'];
	var to = document.querySelector('.container .right .top .name').innerText;
	var msg = $('.my_text').val();
	var info = {'type': 2, 'data':{ 'from': from, 'to': to, 'msg': msg}};
	var res = JSON.stringify(info);
	console.log(res)
    ws.send(res);
}

function appendUserMsg(data){
	
}

/**
 * 获取在线用户列表
 * @author Kevinlee 2019-08-28T10:40:58+0800
 * @param  {[type]} data [description]
 * @return {[type]}      [description]
 */
function appendUserList(data){
	var html     = '';
	var userList = data.data.user_list;
	var length   = userList.length;

	if (!userList) {
		return false;
	}

	var test = [
		'thomas.jpg',
		'bo-jackson.jpg',
		'drake.jpg',
		'louis-ck.jpeg'
	];
	
	for (var i = 0; i < length; i++) {
		html += '<li class="person" data-chat="person'+(i+1)+'">';
        html += '<img src="img/'+test[i]+'" alt="" />';
        html += '<span class="name">'+userList[i]+'</span>';
     	html += '<span class="time">2:09 PM</span>';
     	html += '<span class="preview">I was wondering...</span>';
 		html += '</li>'
	}
	$('.people').append(html);
	document.querySelector('.container .right .top .name').innerHTML= userList[0];
}

/**
 * 切换用户
 * @author Kevinlee 2019-08-28T10:46:10+0800
 * @return {[type]} [description]
 */
function changeTalk(){
	document.querySelector('.chat[data-chat=person1]').classList.add('active-chat');
	document.querySelector('.person[data-chat=person1]').classList.add('active');

	var friends = {
		list: document.querySelector('ul.people'),
		all: document.querySelectorAll('.left .person'),
		name: '' 
	},

	chat = {
		container: document.querySelector('.container .right'),
		current: null,
		person: null,
		name: document.querySelector('.container .right .top .name')
	};


	friends.all.forEach(function (f) {
		f.addEventListener('mousedown', function () {
			f.classList.contains('active') || setAciveChat(f);
		});
	});

	function setAciveChat(f) {
		friends.list.querySelector('.active').classList.remove('active');
		f.classList.add('active');
		chat.current = chat.container.querySelector('.active-chat');
		chat.person = f.getAttribute('data-chat');
		chat.current.classList.remove('active-chat');
		chat.container.querySelector('[data-chat="' + chat.person + '"]').classList.add('active-chat');
		friends.name = f.querySelector('.name').innerText;
		chat.name.innerHTML = friends.name;
	}
}

/**
 * 获取url参数
 * @author Kevinlee 2019-08-28T10:41:41+0800
 */
function GetRequest() {  
   var url = location.search; //获取url中"?"符后的字串  
   var theRequest = new Object();  
   if (url.indexOf("?") != -1) {  
      var str = url.substr(1);  
      strs = str.split("&");  
      for(var i = 0; i < strs.length; i ++) {  
         theRequest[strs[i].split("=")[0]]=unescape(strs[i].split("=")[1]);  
      }  
   }  
   return theRequest;  
}
