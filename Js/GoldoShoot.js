// Premier commentaire de test avec GitHub
AppViewModel = {
	canvasize:
	{
		width: 0,
		height: 0
	},
	points: 0,
	nbNavette: 0,
	nbNavetteShield: 0,
	bonusTranche: 0,
	bonusTrancheIncrement: 50,
	hasBonusGlkGo: false,
	hasBonusAmirale3D: false,
	drawQuadtree: false,
	drawQuadscape: false,
	scapeNodeMinSize: 200, // 200, 100
	campGoldo: 1,
	campVega: 2,
	enableAnim: false,
	inGame: false,
	drawGoldo: true,
	nbclick: 0,
	nbtour: 0,
	continusFireIntervId: 0,
	refreshSessionFireIntervId: 0,
	activity: 0,
	nbcybermine:0,
	nbattachedcybermine:0,

	// for Debug part
	playWithMusic: true,
	playIntro:true,
	playWithSoundEffect: true,
	unicId: 0,
	changeSpeedTime: 0,

	defaultAreaWidth: 1920,
	defaultAreaHeight: 1080,

	bonusVie: 1,
	bonusPlanitron: 2,
	bonusMegavolt: 3,
	bonusAlcorak: 4,
	bonusFossoirak: 5,
	bonusVenusiak: 6,
	bonusOVTerre: 7,
	bonusMegaMach: 8,
	bonusFulguroPoing: 9,
	bonusMortanium: 10,
	bonusCorneAuFulgure: 11,
	BonusGlKgo: 100,
	BonusPlanitronExt: 101,
	BonusAmirale3D: 102
};


KeyEnum = {
	Esc: 27,
	Pause: 80,
	Ctrl: 17,
	Space: 32,
	A: 65,	// Touche magique du tir continue
	X: 88,
	Coma:188,
	Semicolon:190
};



shadow = {
	dx: 20,
	dy: 20,
	factor: 1.02,
};

var gameStartTime = Date.now();
var gameTime = 0;
var modelGoldo = null;
var modelMissilGamma = null;
var modelPlanitron = null;
var modelNbCorneAuFulgure = null;
var modelSoucoupeAmirale = null;
var modelSoucoupeAmirale3D = null;
var modelFulguroPoing = null;




var lastTimeFrame = null;
var requestAnim = 0;
var Goldo = new Goldorak();
var entityTerList = new ChainedList();
var entityList = new ChainedList();
var missilList = new ChainedList();
var otherList = new ChainedList();
var groundDecorsList = new ChainedList();
var animationList = new ChainedList();
var animationListBack = new ChainedList();
var loader = new LoadManager();
var Scape = null;

var scrollZone = null;
var decorManager = null;

var Sequencer = new SequenceManager();

var BackImageTerre01 = null;
var BackImageSpace01 = null;
var BackImageSpace02 = null;
var BackImageSol01 = null;
var BackImageSol02 = null;
var BackImageCloud01 = null;
var BackImageCloud02 = null;
var BackImageOverlayCloud = null;
var BackImageCentre = null;



$(document).ready(function () {


Object.defineProperty(console, '_commandLineAPI',
   { get : function() { throw 'Nooo!' } })
   
   
	$('#idMsgboxLoading').show();

	try {
		window.AudioContext = window.AudioContext || window.webkitAudioContext;
		context = new AudioContext();
	}
	catch (e) {
		alert('Web Audio API is not supported in this browser');
	}


	// Ceci prend bien en compte le Scal de Window pour le text
	// var scale = window.devicePixelRatio;
	window.requestAnimationFrame = window.requestAnimationFrame
		|| window.mozRequestAnimationFrame
		|| window.webkitRequestAnimationFrame
		|| window.msRequestAnimationFrame;

	window.cancelAnimationFrame = window.cancelAnimationFrame
		|| window.mozCancelAnimationFrame;

	var c = document.getElementById("Canvas");
	c.width = screen.width;
	c.height = screen.height;
	AppViewModel.canvasize.width = c.width;
	AppViewModel.canvasize.height = c.height;

	Scape = BuildScapeTree(c.width, c.height);

	loader.loadResource();

	addEventListener('StartGame', function (event) {

		$('#idMsgboxLoading').hide();
		$('#idMsgboxStartGame').show();

		modelGoldo = loader.GetModel('goldorak');
		modelMissilGamma = loader.GetModel('missilgamma');
		modelPlanitron = loader.GetModel('Planitron');
		modelNbCorneAuFulgure = loader.GetModel('NbCorneAuFulgure');
		modelFulguroPoing = loader.GetModel('NbFulguroPoing');

		modelSoucoupeAmirale = loader.GetModel('SoucoupeAmirale');
		modelSoucoupeAmirale3D = loader.GetModel ('SoucoupeAmirale3D');

		Goldo.SetModel(modelGoldo);
		Goldo.Init();
	});

	$("#Canvas").mousemove(function (event) {

		if (AppViewModel.enableAnim == false) return;
		var c = document.getElementById("Canvas");
		
		var loc = windowToCanvas(c, event.clientX, event.clientY);

		if (Goldo.Alcorak != null) RemoveScapeEntity(Goldo.Alcorak);
		if (Goldo.Fossoirak != null) RemoveScapeEntity(Goldo.Fossoirak);
		if (Goldo.Venusiak != null) RemoveScapeEntity(Goldo.Venusiak);
		if (Goldo.OVTerre != null) RemoveScapeEntity(Goldo.OVTerre);

		RemoveScapeEntity(Goldo);

		Goldo.SetPosition(loc.x - Goldo.model.img.width / 2, loc.y - Goldo.model.img.height / 2);

		Scape.SpreadEntity(Goldo);
		if (Goldo.Alcorak != null) Scape.SpreadEntity(Goldo.Alcorak);
		if (Goldo.Fossoirak != null) Scape.SpreadEntity(Goldo.Fossoirak);
		if (Goldo.Venusiak != null) Scape.SpreadEntity(Goldo.Venusiak);
		if (Goldo.OVTerre != null) Scape.SpreadEntity(Goldo.OVTerre);
	});

	$('#idNoSoundTrack').change(function () {
		AppViewModel.playWithMusic = $(this).is(':checked');
		if (AppViewModel.playWithMusic == false) loader.StopCurrentAudio();
	});


	$('#idPlayIntro').change(function () {
		AppViewModel.playIntro = $(this).is(':checked');
	});




	$("#StartTraining").click(function () {

		StartGame(0);
	});

	$("#StartBtn").click(function () {

		var activity = $('#PlayActivity').val();
		if (activity == undefined) activity = -1;
		StartGame(activity);
	});

	function StartGame(activity) {

		$('#idMsgboxStartGame').hide();
		$("#Canvas").show();

		InitGame(activity);
		loader.StopCurrentAudio();
		
		// loader.PlaySound ('GoldorakGo',1);
		// loader.PlayAutoAudioList ();

		AppViewModel.enableAnim = true;

		Goldo.SetPosition(
			(c.width / 2) - modelGoldo.img.width / 2,
			(c.height / 2) - modelGoldo.img.height);
		Scape.SpreadEntity(Goldo);

		StartRefreshSessionFireEvent();

		requestAnim = window.requestAnimationFrame(Render);
	}

	$("#idEntityListBtn").click(function () {
		$('#idMsgboxStartGame').hide();
		$('#idEntityList').show();
	});

	$("#IdCaptchaSaveScore").click(function () {
		
		var idField = '#UserCaptcha';
		var isEmpty = ($('#UserCaptcha').val() == "");
		$('#UserCaptcha').css("background-color", (isEmpty) ? "#FFAAAA" : "#FFFFFF");
		if (!isEmpty)
		{
			$.post( "ProcessSaveScore.php", {
				Activity: AppViewModel.activity,
				Score: AppViewModel.points,
				Nbclick: AppViewModel.nbclick,
				Tour: AppViewModel.nbtour,
				Minutes: gameTime,
				Captcha: $('#UserCaptcha').val()
			})
			.done(function( data ) {
				if (data != "-1")
				{
					$('#idMsgboxStartGame').show();
					$('#idCaptcha').hide();
					$("#Canvas").hide();
					$('#UserCaptcha').val("");
					if ($("#hidden_Start_Intro").val() == 1) loader.PlayIntro();
				}
				else
				{
					$('#idCaptchaError').text("Le nombre est faux");
				}
			});			
		}
		
	});


	$("#idDivCaptchaExitBtn").click(function () {
		$('#idMsgboxStartGame').show();
		$("#Canvas").hide();
		$('#idCaptcha').hide();
		$('#UserCaptcha').val("");
		if ($("#hidden_Start_Intro").val() == 1) loader.PlayIntro();
	});

	$("#idEndGameScoreBtn").click(function () {
		$('#idMsgboxStartGame').hide();
		$('#IdEndGameScore').show();
		$("#hidden_Start_Intro").val(0);
		SetScoreInfo();
	});

	$("#idEndGameScoreExitBtn").click(function () {
		$('#IdEndGameScore').hide();
		$('#idMsgboxStartGame').show();
		$("#Canvas").hide();

		if ($("#hidden_Start_Intro").val() == 1) loader.PlayIntro();
	});

	$("#idCreditBtn").click(function () {
		$('#idMsgboxStartGame').hide();
		$('#idCreditPage').show();
	});

	$("#idScoresListeBtn").click(function () {
		LoadScoreListe();
		$('#idMsgboxStartGame').hide();
		$('#IdScoreListe').show();
	});


	$("#idEntityListExitBtn").click(function () {
		$('#idMsgboxStartGame').show();
		$('#idEntityList').hide();
		$("#Canvas").hide();
	});



	$("#idCreditExitBtn").click(function () {
		$('#idCreditPage').hide();
		$('#idMsgboxStartGame').show();
		$("#Canvas").hide();
	});

	$("#idGameInfoBtn").click(function () {
		$('#idMsgboxStartGame').hide();
		$('#idGameInfo').show();
	});

	$("#idGameInfoExitBtn").click(function () {
		$('#idGameInfo').hide();
		$('#idMsgboxStartGame').show();
		$("#Canvas").hide();
	});

	$("#IdScoreListeExitBtn").click(function () {
		$('#IdScoreListe').hide();
		$('#idMsgboxStartGame').show();
		$("#Canvas").hide();
	});
	
	$("#idHomePage").click(function () {
		window.location.href = "index.php";
	});
	
	$("#IdDeconnect").click(function () {
		window.location.href = 'index.php?disconnect';
	});

	$('.ActivityDropDown').on('change', function () {
		LoadScoreListe();
	});


	$('#Canvas').mousedown(function (event) {

		if (AppViewModel.enableAnim == false || AppViewModel.drawGoldo == false) return;
		switch (event.which) {
			// Left click
			case 1:
				loader.PlaySound('MissilGamma', 0.6);
				// Goldo.MissilGama ();
				Goldo.Fire();
				AppViewModel.nbclick++;
				StartContinuousFireEvent();
				break;
		}
		return false;
	});

	$('#Canvas').mouseup(function (event) {

		if (AppViewModel.enableAnim == false || AppViewModel.drawGoldo == false) return;
		switch (event.which) {
			// Left click
			case 1: StopContinuousFireEvent(); break;
		}

		return false;
	});

	$('#Canvas').mouseleave(function (event) {

		if (AppViewModel.enableAnim == false || AppViewModel.drawGoldo == false) return;
		StopContinuousFireEvent();

		return false;
	});


	document.onkeydown = KeyDownHandler;
	$('#idNoSoundTrack').attr('checked', AppViewModel.playWithMusic);

	// Décommenter les deux lignes ci-dessous pour debugger l'interface
	// sans avoir à charger les ressources images etc ..
	// let event = new CustomEvent("StartGame");
	// window.dispatchEvent(event);
});

function LoadScoreListe() {
	$('#idScoreListe').load("ProcessGetAccountScore.php",
		{
			IdActivity: $('#Activities').val(),
			ScoreSortName: $('#ScoreSortName').val(),
			ScoreSortType: $('#ScoreSortType').val()
		});
}



function ContinuousFire() {
	loader.PlaySound('MissilGamma', 0.6);
	// Goldo.MissilGama ();
	Goldo.Fire();
	AppViewModel.nbclick++;
}


function KeyDownHandler(e) {

	if (AppViewModel.inGame == false) return;

	// if (e.which != 16) alert ("key : " + e.which);
	
	// Si la séquence encours de jeu process la key alors on ne fait rien d'autre.
	if (Sequencer.SequenceProcessKey (e.which) == true) return;

	switch (e.which) {
		// Pause
		case KeyEnum.Pause:
			AppViewModel.enableAnim = !AppViewModel.enableAnim;
			$('#Canvas').css('cursor', (AppViewModel.enableAnim == true && AppViewModel.drawGoldo == true) ? 'none' : 'url(./Images/GoldoCursor.png) 32 20, default');
			loader.PauseCurrentAudio(!AppViewModel.enableAnim);
			break;

		// Escape
		case KeyEnum.Esc:
			AppViewModel.enableAnim = true;
			Goldo.vie = 0;
			break;
	}

	// Si on est en pause ou si Goldorak ne doit pas être dessiné alors on ne fait rien
	if (AppViewModel.enableAnim == false || AppViewModel.drawGoldo == false) return;
	switch (e.which) {
		// Touche magique
		case KeyEnum.A : Goldo.Fire ();break;

		// Control - planitronc
		case KeyEnum.Ctrl: Goldo.MissilPlanitron(); break;

		// Space bar - Cornofulgure
		case KeyEnum.Space: Goldo.CorneAuFulgure(); break;

		// X, Virgule et PointVirgule - Fulguropoing
		case KeyEnum.X: case KeyEnum.Coma: case KeyEnum.Semicolon: Goldo.MissilFulguroPoing(); break;
	}
}

function InitGame(activity) {
	lastTimeFrame = null;

	AppViewModel.activity = activity;
	AppViewModel.nbNavette = 8;
	AppViewModel.nbNavetteShield = 2;

	decorManager = new DecorManager();

	if (scrollZone == null) {

		// Image Ground
		var back = loader.GetModel('BackTerre01');
		BackImageTerre01 = new ScrollBackImage(back.img, SeqEnum.ScrollTypeGround, screen.width, screen.height);
		
		back = loader.GetModel('BackCentre');
		BackImageCentre = new ScrollBackImage(back.img, SeqEnum.ScrollTypeGround, screen.width, screen.height);

		back = loader.GetModel('BackMoon01');
		BackImageSol01 = new ScrollBackImage(back.img, SeqEnum.ScrollTypeGround, screen.width, screen.height);

		back = loader.GetModel('BackMoon02');
		BackImageSol02 = new ScrollBackImage(back.img, SeqEnum.ScrollTypeGround, screen.width, screen.height);

		// Image Sky
		back = loader.GetModel('BackCloud01');
		BackImageCloud01 = new ScrollBackImage(back.img, SeqEnum.ScrollTypeSky, screen.width, screen.height);

		back = loader.GetModel('BackCloud02');
		BackImageCloud02 = new ScrollBackImage(back.img, SeqEnum.ScrollTypeSky, screen.width, screen.height);

		// Image Space
		back = loader.GetModel('BackSpace01');
		BackImageSpace01 = new ScrollBackImage(back.img, SeqEnum.ScrollTypeSpace, screen.width, screen.height);

		back = loader.GetModel('BackSpace02');
		BackImageSpace02 = new ScrollBackImage(back.img, SeqEnum.ScrollTypeSpace, screen.width, screen.height);

		// Images cloud transparent
		back = loader.GetModel('OverlayCloud01');
		BackImageOverlayCloud = new ScrollBackImage(back.img, SeqEnum.ScrollTypeCloud, screen.width, screen.height);

		scrollZone = new ScrollZone(screen.width, screen.height);
	}

	
	// scrollZone.Init(100, BackImageTerre01, BackImageSol02, BackImageOverlayCloud);
	scrollZone.Init(0, BackImageSpace02, BackImageSol02, BackImageOverlayCloud);
	

	AppViewModel.bonusTranche = 75;
	AppViewModel.points = 0;
	// AppViewModel.drawGoldo = true;
	DrawGoldo(true);


	AppViewModel.hasBonusGlkGo = false;
	AppViewModel.hasBonusAmirale3D = false;

	Goldo.Reset();
	
	switch(activity)
	{
		// Type de partie entrainement
		case 0:
		Sequencer.ResetRandom();
		Sequencer.EnableBoard(true);
		break;
		
		// Type de partie autre activités
		// -1  : est une partie libre mais non connéctée
		// 1   : est une partie libre connecté
		// > 1 : est une partie connecté à une autre activité
		default:Sequencer.Reset();
		break;
	}

	FreeResources();
	Scape.FlushTree();
	AppViewModel.inGame = true;

	AppViewModel.nbclick = 0;
	AppViewModel.nbtour = 0;
	gameStartTime = Date.now();
	// StopContinuousFireEvent ();
}

function FreeResources() {
	FreeChainList(entityList);
	FreeChainList(missilList);
	FreeChainList(entityTerList);
	FreeChainList(animationList);
	FreeChainList(animationListBack);
	FreeChainList(groundDecorsList);
	FreeChainList(otherList);
}

function FreeChainList(chainedList) {
	var node = chainedList.firstnode;
	while (node != null) {
		delete node.obj;
		node = node.next;
	}
	chainedList.RemoveAll();
}

function NearestTarget(target, lenFromSrc) {
	this.target = target;
	this.lenFromSrc = lenFromSrc;
}

function GetNearestTargetEntity(srcPosx, srcPosy) {
	var nearTarget01 = GetNearestEntityList(srcPosx, srcPosy, entityList);
	var nearTarget02 = GetNearestEntityList(srcPosx, srcPosy, entityTerList);

	if (nearTarget01 == null && nearTarget02 == null) return null;

	if (nearTarget01 != null && nearTarget02 != null) {
		return (nearTarget01.lenFromSrc < nearTarget02.lenFromSrc) ? nearTarget01.target : nearTarget02.target;
	}

	return (nearTarget01 != null) ? nearTarget01.target : nearTarget02.target;
}

function GetNearestEntityList(srcPosx, srcPosy, list) {
	var lenmem = -1;
	var node = list.firstnode;
	var nodemem = null;
	while (node != null) {
		var entity = node.obj;
		var ddx = srcPosx - node.obj.posx;
		var ddy = srcPosy - node.obj.posy;
		var len = (ddx * ddx + ddy * ddy);

		if (
			entity.IsValid() == true
			&& entity.IsInvulnerable() == false
			&& (entity.posy + entity.model.img.width) > 0
			&& entity.posy < AppViewModel.canvasize.height) {
			if (lenmem == -1) {
				lenmem = len
				nodemem = node;
			}
			else {
				if (len < lenmem) {
					lenmem = len;
					nodemem = node;
				}
			}
		}

		node = node.next;
	}

	if (nodemem != null) return new NearestTarget(nodemem.obj, lenmem);
	return null;
}

function playSound(buffer) {

	var source = context.createBufferSource(); // creates a sound source
	source.buffer = buffer;                    // tell the source which sound to play
	source.connect(context.destination);       // connect the source to the context's destination (the speakers)
	source.start(0);                           // play the source now
	// note: on older systems, may have to use deprecated noteOn(time);
}

function windowToCanvas(canvas, x, y) {
	var bbox = canvas.getBoundingClientRect();

	return {
		x: x - bbox.left * (canvas.width / bbox.width),
		y: y - bbox.top * (canvas.height / bbox.height)
	};
}

function Render(now) {
	now *= 0.001;  // convert to seconds
	if (lastTimeFrame == null) lastTimeFrame = now;
	const deltaTime = now - lastTimeFrame;
	lastTimeFrame = now;

	if (AppViewModel.enableAnim == true) {
		var c = document.getElementById("Canvas");
		var ctx = c.getContext("2d");

		if (Sequencer.IsCurrentSequenceFinished()) {
			Sequencer.CloseCurrentSequence();
			StartSequence();
		}
		else {
			Sequencer.AnimateCurrentSequence(deltaTime);
		}
		
		Sequencer.AnimateOtherPart(deltaTime);

		AppViewModel.changeSpeedTime += deltaTime;

		scrollZone.Animate(deltaTime);
		scrollZone.Draw(ctx);

		Sequencer.DrawCurrentSequence(ctx, false);

		RenderAnimations(groundDecorsList, deltaTime, ctx);
		RenderAnimations(animationListBack, deltaTime, ctx);
		RenderEntities(entityTerList, deltaTime, ctx);
		RenderEntities(entityList, deltaTime, ctx);
		RenderEntities(missilList, deltaTime, ctx);
		RenderEntities(otherList, deltaTime, ctx);

		Goldo.Animate(deltaTime);
		ProcessCollision(Goldo);
		if (Goldo.Alcorak != null) ProcessCollision(Goldo.Alcorak);
		if (Goldo.Fossoirak != null) ProcessCollision(Goldo.Fossoirak);
		if (Goldo.Venusiak != null) ProcessCollision(Goldo.Venusiak);
		if (Goldo.OVTerre != null) ProcessCollision(Goldo.OVTerre);

		RenderAnimations(animationList, deltaTime, ctx);
		
		Sequencer.DrawBoard(ctx);
		if (AppViewModel.drawGoldo == true) Goldo.Draw(ctx);
		
		
		// if (AppViewModel.drawGoldo == true) Goldo.Draw(ctx);
		// RenderAnimations(animationList, deltaTime, ctx);
		// Dessiner le tableau de bord
		// Sequencer.DrawBoard(ctx);

		if (AppViewModel.points >= AppViewModel.bonusTranche) {
			NewBonus();
			AppViewModel.bonusTranche = AppViewModel.points + AppViewModel.bonusTrancheIncrement + randomUniform(1, 21);
		}


		// Pour DEBUG
		// ctx.fillText(' ScrollSpeed : ' + scrollZone.Speed + " " + scrollZone.SpeedTarget + " " + scrollZone.Accel, 200, 500);
		

		if (AppViewModel.drawQuadscape == true) Scape.Draw(ctx);

		Sequencer.DrawCurrentSequence(ctx, true);
		scrollZone.DrawCloud(ctx);

		if (!Goldo.IsValid()) {
			StopGame();
			return;
		}
	}

	requestAnim = window.requestAnimationFrame(Render);
}

function StartContinuousFireEvent() {
	if (AppViewModel.continusFireIntervId == 0) {
		AppViewModel.continusFireIntervId = setInterval(ContinuousFire, 190);
	}
}

function StopContinuousFireEvent() {
	if (AppViewModel.continusFireIntervId != 0) {
		clearInterval(AppViewModel.continusFireIntervId);
		AppViewModel.continusFireIntervId = 0;
	}
}


function StartRefreshSessionFireEvent() {
	if (AppViewModel.refreshSessionFireIntervId == 0) {
		AppViewModel.refreshSessionFireIntervId = setInterval(RefreshSession, 600000);
	}
}

function StopRefreshSessionFireEvent() {
	if (AppViewModel.refreshSessionFireIntervId != 0) {
		clearInterval(AppViewModel.refreshSessionFireIntervId);
		AppViewModel.refreshSessionFireIntervId = 0;
	}
}

function RefreshSession() {
	$.post("ProcessSessionAlive.php");
}



function StopGame() {

	Sequencer.EnableBoard(false);
	loader.PauseCurrentAudio(false);

	StopRefreshSessionFireEvent();

	window.cancelAnimationFrame(requestAnim);
	StopContinuousFireEvent();
	StopAllEntities ();

	AppViewModel.enableAnim = false;
	AppViewModel.inGame = false;

	loader.StopCurrentAudio();
	Sequencer.AbortCurrentSequence();

	if (Goldo.vie == 0) loader.PlayFailed();


	gameTime = Date.now() - gameStartTime;

	$("#hidden_Start_Intro").val(1);

	SetScoreInfo();

	DisplaySaveScore();
	// $('#IdEndGameScore').show();
}


// Pour chaque entités connus on appelle StopGame
// Uné méthode qui permet de réaliser du traitement de dernière minte en fin de de partie
function StopAllEntities ()
{
	StopGameEntityList (groundDecorsList);
	StopGameEntityList (animationListBack);
	StopGameEntityList (entityTerList);
	StopGameEntityList (entityList);
	StopGameEntityList (missilList);
	StopGameEntityList (otherList);
}

function StopGameEntityList (chainedList)
{
	var node = chainedList.firstnode;
	while (node != null)
	{
		var entity = node.obj;
		entity.StopGame();
		node = node.next;
	}
}


function DisplaySaveScore()
{
	if (AppViewModel.activity > 0 && AppViewModel.points > 0)
	{
		$('#idDivImgCaptcha').load('captcha.php', function() {
			SetScoreInfo();
			$('#idCaptcha').show();
		});		
	}
	else
	{
		$('#IdEndGameScore').show();
	}
}

function SetScoreInfo() {
	$('#iTextScore').text(AppViewModel.points + " Pts");
	$('#iTextTour').text("Tour : " + AppViewModel.nbtour);
	$('#iTextClick').text("Click : " + AppViewModel.nbclick);
	
	var gemetime = FormatGameTime(gameTime)
	$('#iTextTemps').text(gemetime);
	
	$('#iCaptchaScore').text(AppViewModel.points + " Pts");
	$('#iCaptchaTour').text("Tour : " + AppViewModel.nbtour);
	$('#iCaptchaClick').text("Click : " + AppViewModel.nbclick);
	$('#iCaptchaTemps').text(gemetime);
	$('#idCaptchaError').text("");
	$('#UserCaptcha').val("");
	$('#UserCaptcha').css("background-color", "#FFFFFF");

}

function FormatGameTime(gameTimeSpent) {
	if (AppViewModel.nbtour == 0) gameTimeSpent = 0;

	var timeDiff = gameTimeSpent / 1000;
	var seconds = Math.floor(timeDiff % 60);

	// Extract integer minutes that don't form an hour using %
	timeDiff = Math.floor(timeDiff / 60);
	var minutes = timeDiff % 60; //no need to floor possible incomplete minutes, becase they've been handled as seconds

	// Extract integer hours that don't form a day using %
	timeDiff = Math.floor(timeDiff / 60);
	var hours = timeDiff % 24; //no need to floor possible incomplete hours, becase they've been handled as seconds

	var timeAsString =
		(hours < 10 ? "0" + hours : hours + "")
		+ ":" +
		(minutes < 10 ? "0" + minutes : minutes + "")
		+ ":" +
		(seconds < 10 ? "0" + seconds : seconds + "");
	return timeAsString;
}


function DrawGoldo(draw) {
	AppViewModel.drawGoldo = draw;
	$('#Canvas').css('cursor', (draw == true) ? 'none' : 'url(./Images/GoldoCursor.png) 32 20, default');
}


function DrawImageList(ctx, imageList, starty) {
	if (imageList != null) {
		for (var i = 1; i <= Object.keys(imageList).length; i++) {
			var img = imageList[i];
			var imgposx = (i - 1) * img.width;
			ctx.drawImage(img, imgposx, starty);
		}
	}
}

function DrawModels(ctx, modelName, nb, starty) {
	for (var i = 1; i <= nb; i++) {
		var model = loader.GetModel(modelName + i);
		var imgposx = (i - 1) * model.img.width;
		ctx.drawImage(model.img, imgposx, starty);
	}
}

function StartSequence() {
	if (AppViewModel.enableAnim == false) return;
	
	if (Sequencer.PlayNext() == false)
	{
		// Ici on est en fin de jeu
		StopGame();
	}
}

function NewBonus() {
	NewBonusType(randomUniform(1, 11)); // Corne au fulgure exclue
}

function NewBonusType(bonusType) {
	var bonusName;
	switch (bonusType) {
		case AppViewModel.bonusPlanitron: bonusName = 'PlanitronBonus'; break;
		case AppViewModel.bonusMegavolt: bonusName = 'BonusMegavolt'; break;
		case AppViewModel.bonusAlcorak: bonusName = 'BonusAlcorak'; break;
		case AppViewModel.bonusFossoirak: bonusName = 'BonusFossoirak'; break;
		case AppViewModel.bonusVenusiak: bonusName = 'BonusVenusiak'; break;
		case AppViewModel.bonusOVTerre: bonusName = 'BonusOVTerre'; break;
		case AppViewModel.bonusMegaMach: bonusName = 'BonusMegaMach'; break;
		case AppViewModel.bonusFulguroPoing: bonusName = 'BonusFulguroPoing'; break;
		case AppViewModel.bonusMortanium: bonusName = 'BonusMortanium'; break;

		case AppViewModel.bonusCorneAuFulgure: bonusName = 'BonusCorneAuFulgure'; break;

		case AppViewModel.BonusGlKgo: bonusName = 'BonusGlKgo'; break;
		case AppViewModel.BonusPlanitronExt: bonusName = 'BonusPlanitronExt'; break;
		case AppViewModel.BonusAmirale3D: bonusName = 'BonusAmirale3D'; break;

		// Par defaut c'est le bonus de la vie
		// si on est au max et bien tampis ps de Bonus
		// et on passe à la tranche suivante
		default:
			if (Goldo.vie >= 5) return;
			bonusName = 'Bonus';
			break;
	}

	var bonus = new Bonus(bonusName);
	bonus.Init();
	otherList.AddObject(bonus);
}

function RenderAnimations(chainedList, deltaTime, ctx) {
	var node = chainedList.firstnode;
	var memnode = null;
	while (node != null) {
		memnode = node;
		var entity = memnode.obj;
		node = node.next;
		if (entity.IsValid() == true) {
			entity.Animate(deltaTime);
			entity.Draw(ctx);
		}
		else {
			chainedList.RemoveNode(memnode);
			entity.OnEnded();
			delete entity;
		}
	}
}

function RenderEntities(chainedList, deltaTime, ctx) {
	var node = chainedList.firstnode;
	var memnode = null;
	while (node != null) {
		memnode = node;
		var entity = memnode.obj;
		node = node.next;
		if (entity.IsValid() == true) {
			RemoveScapeEntity(entity);
			entity.Animate(deltaTime);
			Scape.SpreadEntity(entity);
			ProcessCollision(entity);

			if (entity.IsValid() == true) {
				entity.Draw(ctx);
			}
		}
		else {
			entity.UnsubscribeAll();
			RemoveScapeEntity(entity);
			chainedList.RemoveNode(memnode);
			entity.OnEnded();
			if (entity.CanDeleted() == true) delete entity;
		}
	}
}

function EagleWin() {
	InvalidEntities(entityTerList);
	InvalidEntities(entityList);
	InvalidEntities(missilList);
}

function InvalidEntities(chainedList) {
	var node = chainedList.firstnode;
	var memnode = null;
	while (node != null) {
		memnode = node;
		var entity = memnode.obj;
		node = node.next;
		if (entity != null && entity.CanDeleted() == true) {
			entity.vie = 0;
			entity.SetNoValid();
		}
	}
}

function ProcessCollision(entitySrc) {
	if (entitySrc.ScapeNodeList == null) return;

	// scapenode est un noeud de type terrain
	var scapenode = entitySrc.ScapeNodeList.firstnode;
	while (scapenode != null) {
		// Il faut au moins deux entités sinon cela ne sert à rien de tester
		if (scapenode.obj.EntityList.count > 1) {
			// entitynode est un noeud de type entity
			var entitynode = scapenode.obj.EntityList.firstnode;
			while (entitynode != null) {
				// Prendre l'entité à tester
				var entityDst = entitynode.obj;
				entitynode = entitynode.next;

				// On ne teste pas une entité avec elle même
				if (entityDst == entitySrc) continue;

				// On ne test qu'une entité encore valide
				if (entityDst.IsValid() == false) continue;

				// On ne teste pas des entités du même camp
				// if (entityDst.model.camp == entitySrc.model.camp) continue;
				if (entitySrc.AllowCollisionSameCamp() == false &&
					entityDst.AllowCollisionSameCamp() == false &&
					entityDst.model.camp == entitySrc.model.camp) continue;


				// Missil contre missil pas de collison
				if (entityDst.model.isMissil == true
					&& entitySrc.model.isMissil == true) continue;

				// Tester si il y a collision
				if (entitySrc.Collide(entityDst) == true) {
					entityDst.Hitten = true;
				}
			}
		}
		// Passer au noeud de terrain suivant
		scapenode = scapenode.next;
	}
}

function ProcessGoldorakCollision(entitySrc) {
	if (Goldo.CanBeHitten() == true) {
		// Si il n'y a aucune collision on ne fait rien
		// On teste d'abord la méthode Collide de l'entité
		if (entitySrc.Collide(Goldo) == false) return;
		// on teste ensuite la méthode générale de collision
		// if (Collide (entitySrc,Goldo) == false) return;
		if (entitySrc.model.isBonus == false) {
			entitySrc.Hit();
			NewExplosionPos(Goldo.posx + Goldo.model.img.width / 2, Goldo.posy + Goldo.model.img.height / 2);
			Goldo.Hit()
		}
		else {
			entitySrc.ProcessBonus();
		}
	}
}

function NewExplosionPos(posx, posy) {
	var explosion = new Explosion();
	switch (randomUniform(1, 4)) {
		case 1: explosion.InitExplosion('explosion01', posx, posy, 4, 5, 20); break;
		case 2: explosion.InitExplosion('explosion02', posx, posy, 5, 3, 12); break;
		case 3: explosion.InitExplosion('explosion03', posx, posy, 4, 4, 16); break;
	}

	animationList.AddObject(explosion);

	var facteur = Math.abs(Goldo.posy - posy) / AppViewModel.canvasize.height;
	var volume = 1 - Math.min(facteur, 1);
	volume = Math.min(volume, 0.1);

	loader.PlaySound('Explosion', volume);
}

function NewCrosseExplosionPos(posx, posy) {
	var explosion = new Explosion();
	explosion.InitExplosion('GrosseExplosion', posx, posy, 3, 4, 12);
	explosion.sizeFactor = 2;

	animationList.AddObject(explosion);

	var facteur = Math.abs(Goldo.posy - posy) / AppViewModel.canvasize.height;
	var volume = 1 - Math.min(facteur, 1);
	volume = Math.min(volume, 0.3);

	loader.PlaySound('GrosseExplosion', volume);
}

function NewGroundDecor(decorType, posx, posy, modelName) {
	var decor = new GroundDecor(decorType);
	decor.SetModel(loader.GetModel(modelName));
	decor.posx = posx - decor.model.img.width / 2;
	decor.posy = posy - decor.model.img.height / 2;
	decor.valid = true;

	groundDecorsList.AddObject(decor);
}

function NewSparkBluePos(posx, posy) {
	var spark = new SparkBlue();
	spark.posx = posx;
	spark.posy = posy;

	animationList.AddObject(spark);
}

function NewExplosion(entirySrc, entiryDst) {
	// if (entiryDst.IsValid () == false) return;
	var x, y;

	var missil = (entirySrc.model.isMissil == true) ? entirySrc : entiryDst;
	var target = (missil == entirySrc) ? entiryDst : entirySrc;

	if (target.IsValid()) {
		x = missil.posx;
		y = missil.posy;
	}
	else {
		x = target.posx + target.model.img.width / 2;
		y = target.posy + target.model.img.height / 2;
	}

	this.NewExplosionPos(x, y);
}

function randomUniform(min, max) {
	return Math.floor(Math.random() * (max - min) + min);
}

function randomFloat(min, max) {
	return Math.random() * (max - min) + min;
}

function RemoveScapeEntity(entity) {
	if (entity.ScapeNodeList == null) return;
	var scapenode = entity.ScapeNodeList.firstnode;
	while (scapenode != null) {
		scapenode.obj.EntityList.RemoveObject(entity);
		scapenode = scapenode.next;
	}

	entity.ScapeNodeList.RemoveAll();
}

