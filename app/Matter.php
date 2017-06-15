<?php
namespace App;

Class Matter
{
    public function getAllMatters()
    {
        // Create Soap Object
        $client =  (new \App\Repositories\VlaSoap)->ws_init();
        
        $matters = json_decode($client->GetAllLegalMattersasJSON()->GetAllLegalMattersasJSONResult, true);

        return $matters;
    }

    public function getAllMattersFormated()
    {
        $matters = self::getAllMatters();

        $output = [];

        foreach ($matters as $matter) {            
            $output[] = [
                        'id'    => $matter['MatterID'],
                        'text'  => $matter['ParentName'] . ' - ' . $matter['MatterName']
                        ];
        }

        return $output;
    }

    public function getAllMatterById( $mt_id )
    {
        // Create Soap Object
        $client =  (new \App\Repositories\VlaSoap)->ws_init();
        
        $matter = $client->GetMattersById( array( 'MatterId' => $mt_id ) )->GetMattersByIdResult->LegalMatter;
        if( isset( $matter->MatterQuestions->LegalMatterQuestions ) )
        {
            $questions = [];
            if(sizeof($matter->MatterQuestions->LegalMatterQuestions) == 1)
            {
                $questionId = $matter->MatterQuestions->LegalMatterQuestions->QuestionId;
                $operator = $matter->MatterQuestions->LegalMatterQuestions->Operator;
                $value = $matter->MatterQuestions->LegalMatterQuestions->QuestionValue;
                $questions[ $questionId ] = [ 'operator' => $operator , 'value' => $value ];
                $matter->MatterQuestions = $questions; 
            } else 
            {
                foreach ($matter->MatterQuestions->LegalMatterQuestions as $question) {
                    $questions[ $question->QuestionId ] = [ 'operator' => $question->Operator , 'value' => $question->QuestionValue ];
                }
                $matter->MatterQuestions = $questions;                
            }
        }
        
        return $matter;

    }

    public function saveMatter( $matter ) 
    {
        // Create Soap Object
        $client =  (new \App\Repositories\VlaSoap)->ws_init();
        
        // Current time     
        $date_now = date("Y-m-d");
        $time_now = date("H:i:s");
        $date_time = $date_now . "T" . $time_now;

        $matter['CreatedBy'] = auth()->user()->name;     
        $matter['UpdatedBy'] = auth()->user()->name;     
        $matter['CreatedOn'] = $date_time;       
        $matter['UpdatedOn'] = $date_time;    

        // Create call request        
        $info = [ 'ObjectInstance' => $matter ];
        
        try {

            $response = $client->SaveMatter( $info );

            if( $response->SaveMatterResult ){
                return array( 'success' => 'success' , 'message' => 'New legal matter created.', 'data' => $response->SaveMatterResult );
            } else {
                return array( 'success' => 'error' , 'message' => 'something went wrong.' );
            }
        }
        catch (\Exception $e) {            
            return array( 'success' => 'error' , 'message' =>  $e->getMessage() );      
        }
    }

    public function deleteMatter( $m_id )
    {
        // Create Soap Object
        $client =  (new \App\Repositories\VlaSoap)->ws_init();
        
        // Create call request        
        $info = [ 'RefNumber' => $m_id];

        try {
            $response = $client->DeleteMatter($info);
            if($response->DeleteMatterResult){
                return array( 'success' => 'success' , 'message' => 'Legal matter deleted.' );
            } else {
                return array( 'success' => 'error' , 'message' => 'something went wrong.' );
            }
        }
        catch (\Exception $e) {            
            return array( 'success' => 'error' , 'message' =>  $e->getMessage() );       
        }
    }    
}

