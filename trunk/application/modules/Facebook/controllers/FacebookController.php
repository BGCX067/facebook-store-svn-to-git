<?php
class Facebook_FacebookController extends HTS_Social_Controller_Action
{
	public function publishedPageAction()
	{
		echo '<pre>';
		var_dump( 'Facebook_FacebookController' . $this->_params );
		echo '</pre>';
		die();
		$survey = $this->_params['survey'];
		$survey_pages_db = HTS_Util::getDbTable('SurveyPages');
		$this->view->pages = $survey_pages_db->getAllByIdSurvey($survey['id']);
	}
}