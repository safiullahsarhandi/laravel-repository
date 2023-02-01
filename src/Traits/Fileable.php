<?php

namespace LaravelRepository\Traits;

use App\Models\Media;
use Illuminate\Support\Facades\Storage;

trait Fileable
{
    private function fileNames(){
        return $this->files?? $images = [
            'file' => 'single',
            'files' => 'multiple',
        ];
    }

    protected static function booted(){
        parent::booted();
        self::saved(function(self $model){
            $model->storeFile(files:request()->allFiles());
        });

    } 
    public function files(){
        return $this->morphMany(Media::class,'fileable');
    }

    public function file(){
        return $this->morphOne(Media::class,'fileable');
    }

    protected function uploadFile($file){
        return Storage::putFile('public/media', $file);
    }

    protected function deleteExisitingFiles($keyName,$id){
        $files = $this->files()->where('field_name',$keyName)
        ->where('fileable_id',$id)
        ->get();
        
        foreach($files as $file){
            $filePath = storage_path('app/public/media/'.$file->path);
            Storage::delete($filePath);
            $file->delete();
        }
    }
    public function storeFile($files = [],$keyName = null){
        

        foreach($files as $key => $file){
            if(!$keyName){
                $keyName = $key;
            }
            if(is_array($file)){
                $this->storeFile($file,$keyName);
            }else{
                if($this->isRemovable($keyName))
                {
                    $this->deleteExisitingFiles($keyName,$this->id);
                }
                $path = $this->uploadFile($file);
                // deleting old files
                $this->files()->create([                                        
                    'path' => basename($path), 
                    'field_name' => $keyName,
                    'name' => $file->getClientOriginalName(),
                    'fileable_type' => get_class($this),
                    'fileable_id' => $this->id,

                ]);
                
            }
        }
    }
    
    private function isRemovable($keyName){
        $images = $this->fileNames();
        return \Arr::exists($images,$keyName) && $images[$keyName] == 'single';
    }
    

}
