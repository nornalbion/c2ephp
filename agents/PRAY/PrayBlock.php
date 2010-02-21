<?php
/** \file
*	Defines the PrayBlock abstract superclass, as well as the MakePrayBlock PrayBlock factory.
*/
require_once(dirname(__FILE__).'/AGNTBlock.php');
require_once(dirname(__FILE__).'/CREABlock.php');
require_once(dirname(__FILE__).'/DFAMBlock.php');
require_once(dirname(__FILE__).'/DSAGBlock.php');
require_once(dirname(__FILE__).'/DSEXBlock.php');
require_once(dirname(__FILE__).'/EGGBlock.php');
require_once(dirname(__FILE__).'/EXPCBlock.php');
require_once(dirname(__FILE__).'/FILEBlock.php');
require_once(dirname(__FILE__).'/GENEBlock.php');
require_once(dirname(__FILE__).'/GLSTBlock.php');
require_once(dirname(__FILE__).'/LIVEBlock.php');
require_once(dirname(__FILE__).'/PHOTBlock.php');
require_once(dirname(__FILE__).'/SFAMBlock.php');

/** \name Block Types
* Constants for the various PRAY block types
*/
//@{
/**\brief Value: 'AGNT' */
define('PRAY_BLOCK_AGNT','AGNT'); 
/**\brief Value: 'CREA' */
define('PRAY_BLOCK_CREA','CREA');
/**\brief Value: 'DFAM' */
define('PRAY_BLOCK_DFAM','DFAM');
/**\brief Value: 'DSAG' */
define('PRAY_BLOCK_DSAG','DSAG');
/**\brief Value: 'DSEX' */
define('PRAY_BLOCK_DSEX','DSEX');
/**\brief Value: 'EGG' */
define('PRAY_BLOCK_EGG' ,'EGG' );
/**\brief Value: 'EXPC' */
define('PRAY_BLOCK_EXPC','EXPC');
/**\brief Value: 'FILE' */
define('PRAY_BLOCK_FILE','FILE');
/**\brief Value: 'GENE' */
define('PRAY_BLOCK_GENE','GENE');
/**\brief Value: 'GLST' */
define('PRAY_BLOCK_GLST','GLST');
/**\brief Value: 'LIVE' */
define('PRAY_BLOCK_LIVE','LIVE');
/**\brief Value: 'PHOT' */
define('PRAY_BLOCK_PHOT','PHOT');
/**\brief Value: 'SFAM' */
define('PRAY_BLOCK_SFAM','SFAM');
//@}
/** \name Flags
* Flags used to specify how the block's data is stored.
*/
//@{
/**\brief Value: 1*/
///Whether or not the block is zLib compressed.
define('PRAY_FLAG_ZLIB_COMPRESSED',1);
//@}

/**\brief Creates PrayBlock objects of the correct type.
*	\return An object that is an instance of a subclass of PrayBlock.
* 	\param $blocktype	The type of PRAYBlock, as one of the Block Types defines.
*	\param $prayfile	The PRAYFile object that the PRAYBlock is a child of. This is used to allow blocks to access to each other.
*	\param $name		The name of the PRAYBlock
*	\param $content		The binary content of the PRAYBlock, uncompressed if necessary.
*	\param $flags		The flags given to this PRAYBlock as an integer.
*/
function MakePrayBlock($blocktype,PRAYFile $prayfile,$name,$content,$flags) {
	switch($blocktype) {
		//agents
		case PRAY_BLOCK_AGNT:
			return new AGNTBlock($prayfile,$name,$content,$flags);
		case PRAY_BLOCK_DSAG:
			return new DSAGBlock($prayfile,$name,$content,$flags);
		case PRAY_BLOCK_LIVE:
			return new LIVEBlock($prayfile,$name,$content,$flags); //sea monkeys agent files.

		//egg
		case PRAY_BLOCK_EGG:
			return new EGGBlock($prayfile,$name,$content,$flags);

		//starter families
		case PRAY_BLOCK_DFAM:
			return new DFAMBlock($prayfile,$name,$content,$flags);
		case PRAY_BLOCK_SFAM:
			return new SFAMBlock($prayfile,$name,$content,$flags);
		
		//exported creatures
		case PRAY_BLOCK_EXPC:
			return new EXPCBlock($prayfile,$name,$content,$flags);
		case PRAY_BLOCK_DSEX:
			return new DSEXBlock($prayfile,$name,$content,$flags);

		case PRAY_BLOCK_CREA:
			return new CREABlock($prayfile,$name,$content,$flags);

		//creature photos
		case PRAY_BLOCK_PHOT:
			return new PHOTBlock($prayfile,$name,$content,$flags);

		//creature history
		case PRAY_BLOCK_GLST:
			return new GLSTBlock($prayfile,$name,$content,$flags);

		//creature genetics
		case PRAY_BLOCK_GENE:
			return new GENEBlock($prayfile,$name,$content,$flags);
	
		//files
		case PRAY_BLOCK_FILE:
			return new FILEBlock($prayfile,$name,$content,$flags);

	}
}

/**\brief Abstract class to represent PRAY blocks*/
/**
* This class represents a PRAY block
*/
abstract class PrayBlock {
	private $prayfile;
	private $content;
	private $name;
	private $type;	

	public function PrayBlock($prayfile,$name,$content,$flags,$type) {
		if($prayfile instanceof PRAYFile) {
			$this->prayfile = $prayfile;
		} else {
			$prayfile = null;
		}
		$this->name = $name;
		$this->content = $content;
		$this->flags = $flags;
		$this->type = $type;
	}
	private function EncodeBlockHeader($specificlength) {
		$compiled = $this->GetType();
		$compiled .= substr($this->GetName(),0,128);
		$len = 128 - strlen($this->GetName());
		for($i=0;$i<$len;$i++) {
			$compiled .= "\0";
		}
		$compiled .= pack('VVV',$specificlength,$specificlength,0);
	}
	public function GetName() {
		return $this->name;
	}
	public function GetData() { //uncompressed if compressed.
		return $this->content;
	}
	public function GetType() {
		return $this->type;
	}
	public function GetFlags() {
		return $this->flags;
	}
	public function IsFlagSet($flag) {
		return ($this->flags & $flag == $flag);
	}
	public function GetLength() {
		return strlen($this->content);
	}
	public function GetPrayFile() {
		return $this->prayfile;
	}
	protected function SetData($content) {
		$this->content = $content;
	}
	public function Compile() {
		$compiled  = $this->EncodeBlockHeader(strlen($this->GetData));
		$compiled .= $this->GetData();
	}
}
?>
