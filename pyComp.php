<?php 

    class pyCompiler 
    {
        private $code;

        private $vars = [];

        public function __construct(string $code)
        {
            $this->code = $code;
        }

        public function run()
        {
            $lines = $this->parse_lines($this->code);
            $lines = array_filter($lines);
            
            foreach ($lines as $line)
            {
                $line = trim($line);

                if (!empty($line))
                {
                    
                    $tokens = $this->handle_line ($line);

                    if ($tokens)
                    {
                        $this->handle_tokens($tokens);
                    }
                }
            }

            
        }

        public function parse_lines ($lines)
        {
            $lines = explode("\n" , $lines);
            return $lines;
        }

        public function handle_line ($line)
        {

            $line = str_replace("==" , " == " , $line);
            $line = str_replace("  ==  " , " == " , $line);
            $line = str_replace("  !=  " , " != " , $line);
            $line = str_replace(">" , " > " , $line);
            $line = str_replace("  >  " , " > " , $line);
            $line = str_replace("<" , " < " , $line);
            $line = str_replace("  <  " , " < " , $line);
            $line = str_replace("<=" , " <= " , $line);
            $line = str_replace("  <=  " , " <= " , $line);
            $line = str_replace(">=" , " >= " , $line);
            $line = str_replace("  >=  " , " >= " , $line);
            $line = str_replace("(" , " ( " , $line);
            $line = str_replace("  (  " , " ( " , $line);
            $line = str_replace(")" , " ) " , $line);
            $line = str_replace("  )  " , " ) " , $line);
            $line = str_replace(":" , " : " , $line);
            $line = str_replace("  :  " , " : " , $line);
            $line = str_replace(";" , " ; " , $line);
            $line = str_replace("  ;  " , " ; " , $line);
            $line = str_replace("*" , " * " , $line);
            $line = str_replace("  *  " , " * " , $line);
            $line = str_replace("+" , " + " , $line);
            $line = str_replace("  +  " , " + " , $line);
            $line = str_replace("-" , " - " , $line);
            $line = str_replace("  -  " , " - " , $line);
            $line = str_replace("/" , " / " , $line);
            $line = str_replace("  /  " , " / " , $line);
            
            if (strpos("==" , $line) == false) {
                $line = str_replace("=" , " = " , $line);
                $line = str_replace(" =" , " = " , $line);
                $line = str_replace("= " , " = " , $line);
                $line = str_replace("  =  " , " = " , $line);
            }

            $line = str_replace("=  =" , "==" , $line);
            $line = str_replace("! =" , "!=" , $line);
            $line = str_replace("  =" , " =" , $line);
            $line = str_replace("=  " , "= " , $line);

            // var_dump($line);

            $line = trim ($line);

            return explode (" " , $line);
            
        }

        public function handle_tokens($tokens)
        {

            if ($tokens[0] == 'if')
            {
                $this->handle_if($tokens);
            }
            if ($tokens[0] == 'print')
            {
                $this->handle_print($tokens);
            }
            if (strlen($tokens[0]) == 1)
            {
                $this->handle_assignment($tokens);
            }
            
            
        }

        public function handle_if($tokens)
        {
            //seperate if & else
            $line = implode(" " , $tokens);
            $line = explode(";" , $line);
            $line[1] = trim ($line[1]);

            //if condition
            $condition = trim ( explode(" : " , $line[0])[0] );
            $condition = str_replace ("if ( " , "" , $condition);
            $condition = str_replace ("(" , "" , $condition);
            $condition = str_replace ("if " , "" , $condition);
            $condition = str_replace ("if" , "" , $condition);
            $condition = str_replace (" )" , "" , $condition);
            $condition = trim ($condition);

            $temp = explode(" " , $condition);
            $temp = array_filter($temp);

            if (count($temp) > 1) 
            {
                $operator = $temp[1];

                //var1
                if (intval($temp[0]) != 0 && is_int(intval($temp[0])))
                {
                    $var1 = intval($temp[0]);
                } elseif (isset($this->vars[$temp[0]])) {
                    $var1 = $this->vars[$temp[0]];
                } else {
                    echo "Error ! Wrong If Variables.";
                }

                //var2
                if (intval($temp[2]) != 0 && is_int(intval($temp[2])))
                {
                    $var2 = intval($temp[2]);
                } elseif (isset($this->vars[$temp[2]])) {
                    $var2 = $this->vars[$temp[2]];
                } else {
                    echo "Error ! Wrong If Variables.";
                }
                
                $result = $this->handle_operator($operator , $var1 , $var2);
            } else {
                $result = $temp[0];
            }

            if ($result == true) {
                //operation
                $operation = trim ( explode(" : " , $line[0])[1] );
                $this->handle_tokens(explode(" " , $operation));
            } else {
                //else
                if (isset($line[1])) 
                {
                    $this->handle_else(explode(" " , $line[1]));
                }
            }

        }

        public function handle_operator ($operator , $var1 , $var2)
        {
            switch ($operator)
                {
                    case '==' :
                        return $var1 == $var2;
                    case '!=' :
                        return $var1 != $var2;
                    case '>' :
                        return $var1 > $var2;
                    case '<' :
                        return $var1 < $var2;
                    case '<=' :
                        return $var1 <+ $var2;
                    case '>=' :
                        return $var1 >= $var2;
                }
        }

        public function handle_else($tokens)
        {
            $line = implode(" " , $tokens);
            $operation = trim (explode(" : " , $line) [1]);
            $this->handle_tokens (explode(" " , $operation));
        }

        public function handle_print($tokens)
        {
            $print = trim( implode (" " , $tokens) );
            $print = str_replace ("print ( " , "" , $print);
            $print = str_replace ("print " , "" , $print);
            $print = str_replace ("print" , "" , $print);
            $print = str_replace (" )" , "" , $print);
            $print = str_replace (")" , "" , $print);
            $print = str_replace (" (" , "" , $print);
            $print = str_replace ("(" , "" , $print);
            $print = str_replace ("'" , "" , $print);
            
            echo ($print);
        }

        public function handle_assignment($tokens)
        {

            if (count ($tokens) == 3) 
            {
                $assignment = $tokens[1];
                if ($assignment == "=")
                {
                    
                    if ( is_int (intval($tokens[2])) )
                    {
                        $this->vars [$tokens[0]] = intval($tokens[2]);
                    } elseif ( isset($this->vars[$tokens[2]]) )
                    {
                        $this->vars [$tokens[0]] = intval($this->vars[$tokens[2]]);
                    } else {
                        echo "Error ! Wrong assignment Variables.";
                    }

                } else {
                    echo "Error ! Wrong assignment Operator.";
                }

            } elseif (count ($tokens) > 3)
            {
                
                $token = implode (" " , $tokens);
                $token = explode ("=" , $token);
                $token[1] = trim($token[1]);
                $token[0] = trim($token[0]);
                
                $handlers = explode(" " , $token[1]);
                
                //math
                $operator = $handlers[1];
                //vr1
                if ( is_int( $handlers[0] ) )
                {
                    $var1 =  intval( $handlers[0] );
                } elseif ( isset($this->vars[$handlers[0]]) )
                {
                    $var1 = intval ( $this->vars[$handlers[0]] );
                } else {
                    echo "Error ! Wrong assignment Variable.";
                }

                //var2
                if ( is_int( intval($handlers[2])) )
                {
                    $var2 =  intval( $handlers[2] );
                } elseif ( isset($this->vars[$handlers[2]]) )
                {
                    $var2 = intval ( $this->vars[$handlers[2]] );
                } else {
                    echo "Error ! Wrong assignment Variable.";
                }
                
                $result = $this->handle_assign_operator($operator , $var1 , $var2);
                
                $this->vars[$token[0]]  = $result;

            } else {
                echo "Error ! Wrong assignment Format.";
            }
        }

        public function handle_assign_operator($operator , $var1 , $var2)
        {
            switch ($operator)
            {
                case '*' :
                    return $var1 * $var2;
                case '/' :
                    return $var1 / $var2;
                case '+' :
                    return $var1 + $var2;
                case '-' :
                    return $var1 - $var2;
            }
        }

    }
    
    $code = 
    "
    y = 14
    x = y / 2
    if x != 7 : print 'not equal' ; else : print 'equal'
    ";
    $obj = new pyCompiler($code);
    $obj->run();
    
    //Code Samples :
    $code0 = 
    "
    y = 14
    x = 10
    if x != y : print 'not equal' ; else : print 'equal'
    ";
    $code00 = 
    "
    y = 14
    x = y / 2
    if x != 7 : print 'not equal' ; else : print 'equal'
    ";
    $code1 =
    "
    y=14
    x=y/2
    if(x==7):print('equal');else:print('not equal')
    ";
    $code2 =
    "
    y=14
    x=y/2
    if (x==7):print('equal');else:print('not equal')
    ";
    $code3 =
    "
    y =14
    x =y/2
    if (x==7):print('equal');else:print('not equal')
    ";
    $code4 =
    "
    y= 14
    x= y/2
    if (x==7):print('equal');else:print('not equal')
    ";
    $code5 =
    "
    y = 14
    x = y/2
    if (x==7):print('equal');else:print('not equal')
    ";
    $code6 =
    "
    y = 14
    x = y / 2
    if (x==7):print('equal');else:print('not equal')
    ";
    $code7 =
    "
    y = 14
    x = y / 2
    if ( x==7 ):print('equal');else:print('not equal')
    ";
    $code8 =
    "
    y = 14
    x = y / 2
    if ( x == 7 ):print('equal');else:print('not equal')
    ";
    $code9 =
    "
    y = 14
    x = y / 2
    if ( x == 7 ) : print ('equal') ; else : print ('not equal')
    ";
    $code10 =
    "
    y = 14
    x = y / 2
    if ( x == 7 ) : print ( 'equal' ) ; else : print ( 'not equal' )
    ";
    $code11 =
    "
    y = 14
    x = y / 2
    if x == 7 : print 'equal' ; else : print 'not equal'
    ";
    $code12 = 
    "
    y = 14
    x = 14
    if x == y : print 'equal' ; else : print 'not equal'
    ";

    //Ebd Code Samples
?>