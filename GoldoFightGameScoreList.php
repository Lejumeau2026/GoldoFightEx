		<div id="IdScoreListe" class="DivModal noSelect" style="padding-top: 0px;">
			<div class="BoxScoresListe Zoom">
				<button id="IdScoreListeExitBtn" class="ExitBtn" style="float:left">X</button>
				<p style="clear:left;text-align:center;vertical-align: middle;">
					<img src="Images/Alcorak.png" class="ImgTitle" />
					<span style="text-align: center;vertical-align: middle;font-size:28px">Liste de vos scores</span>
					<img src="Images/Alcorak.png" class="ImgTitle" />
				</p>

				<hr>


				<div class="ScoreFilterPanel">
					<div style="float:left;margin-right:10px;">
						<p class="ScoreFilterItem">Liste des activités</p>
						<select class="ActivityDropDown" name="Activities" id="Activities">
							<option value="0">Toutes</option>
							<?php
							if ($activities != null) {
								foreach ($activities as $activity) {
									echo "<option value=\"$activity->IdActivity\">$activity->Nom</option>";
								}
							}
							?>
						</select>
					</div>

					<div style="float:left;margin-right:10px">
						<p class="ScoreFilterItem">Trier par</p>
						<select class="ActivityDropDown" name="ScoreSortName" id="ScoreSortName">
							<option value="2">Date</option>
							<option value="1">Score</option>
							<option value="3">Activite</option>
						</select>
					</div>

					<div style="float:left">
						<p class="ScoreFilterItem">Sens du tri</p>
						<select class="ActivityDropDown" name="ScoreSortType" id="ScoreSortType">
							<option value="2">Décroissant</option>
							<option value="1">Croissant</option>
						</select>
					</div>

				</div>
				<div style="clear:left" class="ScoresListeDiv" id="idScoreListe"></div>
				<hr><div style="text-align:center">Affichage de 100 scores Max. Vous avez des filtres pour afficher vos derniers scores ou les meileurs etc ...</div>
			</div>
		</div>