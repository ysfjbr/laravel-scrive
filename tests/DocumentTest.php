<?php
use Gnm\Scrive\Scrive;
use \Gnm\Scrive\Model\Document;
class DocumentTest extends \Gnm\Scrive\TestCase
{
    /** @test */
    public function it_can_create_new_document(){
        $document = Scrive::document()->create()->data;
        $this->assertNotNull($document);
    }

    /** @test */
    public function it_can_create_new_document_with_file(){
        $document = Scrive::document()->create(__DIR__.'/testfiles/pdf-sample.pdf')->data;
        $this->assertNotNull($document);
        $this->assertNotNull($document->file);
        $this->assertEquals($document->file->name, 'pdf-sample.pdf');
        $this->assertFalse($document->is_saved);
    }

    /** @test */
    public function it_can_get_existing_document(){
        $document = Scrive::document()->create(null, true);

        $existingDocument = Scrive::document($document->id)->get();
        $this->assertNotNull($existingDocument);
        $this->assertEquals($document->data->id, $existingDocument->data->id);
    }

    /** @test */
    public function it_can_list_all_existing_documents(){
        $document = Scrive::document()->create(null, true);
        $documents = Scrive::document()->list(0,1);
        $this->assertNotEmpty($documents);

    }

    /** @test */
    public function it_can_start_the_signing_proccess(){
        $document = Scrive::document()->create(__DIR__.'/testfiles/pdf-sample.pdf', true);
        $document->start();
        $this->assertEquals($document->data->status, 'pending');
    }

    /** @test */
    public function it_can_delete_document(){
        $document = Scrive::document()->create(__DIR__.'/testfiles/pdf-sample.pdf', true);
        $document->delete();
        $this->assertTrue($document->data->is_deleted);
    }

    /** @test */
    public function it_can_download_document_file(){
        $document = Scrive::document()->create(__DIR__.'/testfiles/pdf-sample.pdf', true);
        $this->assertEquals(get_class($document->file()),'GuzzleHttp\Psr7\Stream');
    }

    /** @test */
    public function it_can_add_parties_when_creating_an_document(){
        $document = Scrive::document()->create(__DIR__.'/testfiles/pdf-sample.pdf', true);
        $document->addParty('test testsson', 'test@example.se', 'test company');
        $update = Scrive::document($document->id)->get();
        $this->assertCount(2, $update->data->parties);
    }

    /** @test */
    public function it_can_cancel_document(){
        $document = Scrive::document()->create(__DIR__.'/testfiles/pdf-sample.pdf', true);
        $document->start();
        $document->cancel();
        $this->assertEquals('canceled', $document->data->status);
        
    }


}
