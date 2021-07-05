<?php
/**
 * Given a class or a method, looks for particular attributes inside the doc-comments for that class/method.
 * Also has a few helper functions for tasks which this was introduced for (i.e. method authentication).
 * @author Peter Serwylo (peter@ivt.com.au)
 */
class AttributeFinder
{

    private $className;
    private $methodName;
	private $docComments;
	
	public function __construct( $class, $method = "")
	{
		if ( strlen( trim( $method ) ) )
		{
			$reflect = new ReflectionMethod( $class, $method );
		}
		else 
		{
			$reflect = new ReflectionClass( $class );
		}
	
        $this->className = $class;
        $this->methodName = $method;
		$this->docComments = $reflect->getDocComment();
	}
	
	/**
	 * Looks for the attribute in the doc comments of the specified class/function.
	 * Returns an array, because there can be multiple's of any attribute 
	 * (like in standard PHP: param, in brilliance: access).
	 * @param string $attribute
	 * @return array List of attribute <i>values</i> matching $attribute.
	 */
	private function getAttributes( $attribute )
	{
		if ( substr( $attribute, 0, 1 ) != "@" )
		{
			$attribute = "@" . $attribute;
		}
		
		$tag = trim( $tag );
		
		$matches = array();
		preg_match_all( "/" . $attribute . "(.*)/", $this->docComments, $matches );
		
		if ( isset( $matches[1] ) )
		{
			return $matches[1];
		}
		
		return array();
		
	}
	
	/**
	 * Parses the attributes, collecting every "@access" tag.
	 * Each tag can have a comment as well, but only the first "word" is examined and expected to be an int.
	 * In the example tag of: 
	 * 
	 *	 	"@access admin Super user can have access because ..."
	 * 
     * The 'admin' value will get extracted and added to the array of required access.
	 * @return array List of permission's which are allowed to access this method.
	 */
	public function getAccess()
	{
		$matches = $this->getAttributes( "@access" );
		
        $access = array();
		foreach( $matches as $match )
		{
            $match = trim( $match );
			
			if ( strlen( $match ) == 0 )
			{
				$error = 
					"Invalid @access syntax for " . $this->className . "::" . $this->methodName . "(). " . 
					"Expecting an integer as the first value after the attribute tag, '" . $parts[0] . "' given.";
				trigger_error( $error, E_USER_WARNING );
			}
			else
			{
                $parts = split( " ", $match, 2 );
                $permissionRequired = trim( $parts[0] );
				$access[] = $permissionRequired;
			}
		}
		return $access;
	}
	
}
