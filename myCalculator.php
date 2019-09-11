<?php
/*
 * This file consists of the definition of MyCalculator class and it's members.
 * It initializes the object of MyCalculator class and executes the members of 
 * the class by passing the arithmetic expression input string.
 */

/**
 * MyCalculator class is to take an arithmetic expression as an input string, 
 * evaluate it and return the output.
 *
 * @author Sanyasi Rao
 */
class MyCalculator
{
    /**
     * This method is to take an arithmetic expression as a parameter and to 
     * split the expression to smaller parts as per the BODMAS brackets order 
     * '(,{,['.
     * 
     * @param string $input  A string of the given arithmetic expression input.
     * 
     * @return float  A decimal or integer number resulted from the evaluation 
     * of the given arithmetic expression input string.
     */
    public function evaluateInputString($input)
    {
        $start = strrpos($input, "(");
        if ($start !== FALSE) {
            $end = strpos($input, ")", $start);
            $inputStringChunk = substr($input, ($start + 1), ($end - $start - 1));
            $inputStringChunkValue = $this->evaluateInputStringChunk($inputStringChunk);
            $input = substr_replace($input, $inputStringChunkValue, $start, $end - $start + 1);
            echo '<br />$input ' . $input;
            return $this->evaluateInputString($input);
        }
        $start = strrpos($input, "{");
        if ($start !== FALSE) {
            $end = strpos($input, "}", $start);
            $inputStringChunk = substr($input, ($start + 1), ($end - $start - 1));
            $inputStringChunkValue = $this->evaluateInputStringChunk($inputStringChunk);
            $input = substr_replace($input, $inputStringChunkValue, $start, $end - $start + 1);
            return $this->evaluateInputString($input);
        }
        $start = strrpos($input, "[");
        if ($start !== FALSE) {
            $end = strpos($input, "]", $start);
            $inputStringChunk = substr($input, ($start + 1), ($end - $start - 1));
            $inputStringChunkValue = $this->evaluateInputStringChunk($inputStringChunk);
            $input = substr_replace($input, $inputStringChunkValue, $start, $end - $start + 1);
            return $this->evaluateInputString($input);
        }
        return $this->evaluateInputStringChunk($input);
    }
    
    /**
     * This method is to evaluate the chunk of the given expression and prepare 
     * an array of operands and operators. The operators priority is as per 
     * BODMAS rule '^/*+-'. '^' represents power of.
     * 
     * @param string $inputStringChunk  A string of arithmetic expression chunk
     * inside the brackets of the given expression.
     * 
     * @return float  A decimal or integer resulted from the evaluation of the 
     * arithmetic expression chunk.
     */
    public function evaluateInputStringChunk($inputStringChunk) {
        $operands = array();
        $operators = array();
        $lastOperand = '';
        //echo '<br />' . var_dump($inputStringChunk) . '<br />';
        for ($i = 0; $i < strlen($inputStringChunk); $i++) {
            //echo '<br />'. $inputStringChunk[$i];
            if ((string)strpbrk($inputStringChunk[$i], "^/*+-")) {
                $operators[] = $inputStringChunk[$i];
                $operands[] = $lastOperand;
                $lastOperand = '';
            } else if (($inputStringChunk[$i] == "O") && ($inputStringChunk[$i + 1] == "f")) {
                $operators[] = "Of";
                $operands[] = $lastOperand;
                $lastOperand = '';
            } else if ($inputStringChunk[$i] != "f") {
                $lastOperand .= $inputStringChunk[$i];
            }
            //var_dump($lastOperand);
        }
        $operands[] = $lastOperand;
        echo '<pre>';
        $arithmeticExpResult = $this->evaluateArithmeticExpression($operands, $operators);
        return $arithmeticExpResult;
    }
    
    /**
     * This method is to take the prepared operands and operators and compute 
     * the values as per BODMAS rule for operators precedence 'Of,^,/,*,+,-'.
     * 
     * @param array $operands  An array of operands of the arithmetic 
     * expression.
     * 
     * @param array $operators  An array of operators of the arithmetic 
     * expression.
     * 
     * @return array  A decimal or integer number resulted from the computation
     * of the given operands as per the given operators.
     */
    public function evaluateArithmeticExpression(array $operands, array $operators)
    {
        $result = 0;
        //print_r($operands);
        //print_r($operators);
        if (empty($operators)) {
            return $operands[0];
        }
        while (!empty($operators)) {
            $elementKey = array_search("Of", $operators);
            //var_dump($elementKey);
            if ($elementKey !== FALSE) {
                $nextVal = $this->getNext($operands, $elementKey);
                $result = $operands[$elementKey] * $nextVal;
                $nextKey = array_search($nextVal, $operands);
                $operands[$nextKey] = $result;
                unset($operands[$elementKey]);
                unset($operators[$elementKey]);
                if (array_search("Of", $operators)) {
                    continue;
                }
            }
            $elementKey = array_search("^", $operators);
            //var_dump($elementKey);
            if ($elementKey !== FALSE) {
                //echo '<br />^';
                $nextVal = $this->getNext($operands, $elementKey);
                $result = pow($operands[$elementKey], $nextVal);
                $nextKey = array_search($nextVal, $operands);
                $operands[$nextKey] = $result;
                unset($operands[$elementKey]);
                unset($operators[$elementKey]);
                //print_r($operands);
                //print_r($operators);
                if (array_search("^", $operators)) {
                    continue;
                }
            }
            $elementKey = array_search("/", $operators);
            //var_dump($elementKey);
            if ($elementKey !== FALSE) {
                $nextVal = $this->getNext($operands, $elementKey);
                $result = $operands[$elementKey] / $nextVal;
                $nextKey = array_search($nextVal, $operands);
                $operands[$nextKey] = $result;
                unset($operands[$elementKey]);
                unset($operators[$elementKey]);
                //print_r($operands);
                //print_r($operators);
                if (array_search("/", $operators)) {
                    continue;
                }
            }
            $elementKey = array_search("*", $operators);
            //var_dump($elementKey);
            if ($elementKey !== FALSE) {
                $nextVal = $this->getNext($operands, $elementKey);
                $result = $operands[$elementKey] * $nextVal;
                $nextKey = array_search($nextVal, $operands);
                $operands[$nextKey] = $result;
                unset($operands[$elementKey]);
                unset($operators[$elementKey]);
                if (array_search("*", $operators)) {
                    continue;
                }
            }
            //var_dump($elementKey);
            $elementKey = array_search("+", $operators);
            //var_dump($elementKey);
            if ($elementKey !== FALSE) {
                $nextVal = $this->getNext($operands, $elementKey);
                $result = $operands[$elementKey] + $nextVal;
                $nextKey = array_search($nextVal, $operands);
                $operands[$nextKey] = $result;
                unset($operands[$elementKey]);
                unset($operators[$elementKey]);
                if (array_search("+", $operators)) {
                    continue;
                }
            }
            $elementKey = array_search("-", $operators);
            //var_dump($elementKey);
            if ($elementKey !== FALSE) {
                $nextVal = $this->getNext($operands, $elementKey);
                $result = $operands[$elementKey] - $nextVal;
                $nextKey = array_search($nextVal, $operands);
                $operands[$nextKey] = $result;
                unset($operands[$elementKey]);
                unset($operators[$elementKey]);
                if (array_search("-", $operators)) {
                    continue;
                }
            }
        }
        return $result;
    }
    
    /**
     * This method is to get the next value after the given key in the given 
     * array of operands.
     * 
     * @param array $array  An array of operands.
     * 
     * @param int $key  An integer represents the key of an operand.
     * 
     * @return int  An integer of the next value after the given key of the 
     * operands array.
     */
    public function getNext($array, $key) {
        $currentKey = key($array);
        while ($currentKey !== null && $currentKey != $key) {
            next($array);
            $currentKey = key($array);
        }
        return next($array);
     }
}

// Take arithmetic expression as an input string.
echo $input = "2 / [ 1 Of { 6 + ( 4 / 2 * 3 + 4 - 5 ) - 5 } / 6 ] ^ 7";
//echo '<br />Answer: 2<br />';
//echo $input = "1044 / [(3/4) Of (71 + 65) - 15]";
//echo '<br />Answer: 12';
//echo $input = "(7 * 7) ^ 3 / (49 * 7) ^ 3 * (2401) ^ 2";
//echo '<br />Answer: 16807';
//echo $input = "(5.4 / 6 + 0.3) ^ 2";
//echo '<br />Answer: 1.44';
//echo $input = "((27 / 5 * 4) / 15)";
//echo '<br />Answer: 1.44';
//echo $input = "36 ^ 3 * 4096 ^ (1/2) * 72 * 18 / (9 ^ 3 * 72 ^ 2)";
//echo '<br />Answer: 1024';
//echo $input = "4 ^ 5";
//echo '<br />Answer: 1024';
//echo $input = "(25/9) / (11/2) * (693/350)";
//echo '<br />Answer: 1';
//echo $input = "(1/3) Of (5/7) Of (147/175) Of 5";
//echo '<br />Answer: 1(Approximately)';

if (!empty($input)) {
    // Remove spaces from the given input string
    $input = preg_replace('/\s/', '', $input);
    // Create 'MyCalculator' object
    $myCalculator = new MyCalculator();
    /* Display output resulted from the evaluation of the given arithmetic 
     * expression input string.
     */
    echo $myCalculator->evaluateInputString($input);
}