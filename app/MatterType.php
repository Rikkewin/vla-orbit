<?php
namespace App;

Class MatterType
{

	public function getAllMatterTypes()
	{
		// Create Soap Object
        $client =  (new \App\Repositories\VlaSoap)->ws_init();
        
        $matter_types = json_decode($client->GetAllLegalMatterTypessasJSON()->GetAllLegalMatterTypessasJSONResult);

        foreach ($matter_types as $matter_type) {
            $result[]  = [ 
                            'MatterTypeID' => $matter_type->MatterTypeID,
                            'MatterTypeName' => $matter_type->MatterTypeName,
                            'CreatedBy' => $matter_type->CreatedBy,
                            'UpdatedBy' => $matter_type ->UpdatedBy,
                            'CreatedOn' => $matter_type->CreatedOn,
                            'UpdatedOn' => $matter_type->UpdatedOn,
                        ];
        }

        return $result;
	}
    
    public function saveMatterType( $matter_type ) 
    {
        // Create Soap Object
        $client =  (new \App\Repositories\VlaSoap)->ws_init();
        
        // Create call request        
        $info = [ 'ObjectInstance' => [
                        
                        'MatterTypeName' => $matter_type['title'], // $matter_type['title']
                        'CreatedBy' => auth()->user()->name,
                        'CreatedOn' => '2017-05-11T16:00:00',
                        'UpdatedBy' => auth()->user()->name,
                        'UpdatedOn' => '2017-05-11T16:00:00'
                        ]                    
                ];
        try {
            $response = $client->SaveMatterType($info);            
            if($response->SaveMatterTypeResult){
                return array( 'success' => 'success' , 'message' => 'Legal matter Type deleted.' );
            } else {
                return array( 'success' => 'error' , 'message' => 'something went wrong.' );
            }
        }
        catch (\Exception $e) {            
            return array( 'success' => 'error' , 'message' =>  $e->getMessage() );           
        }
    }


    public function deleteMatter($mt_id)
    {

        // Create Soap Object
        $client =  (new \App\Repositories\VlaSoap)->ws_init();
        
        // Create call request        
        $info = [ 'RefNumber' => $mt_id];

        try {
            $response = $client->DeleteMatterType($info);
            // Redirect to index        
            if($response->DeleteMatterTypeResult){
                return array( 'success' => 'success' , 'message' => 'Legal matter Type deleted.' );
            } else {
                return array( 'success' => 'error' , 'message' => 'something went wrong.' );
            }
        }
        catch (\Exception $e) {            
            return array( 'success' => 'error' , 'message' =>  $e->getMessage() );              
        }

    }
    
}

