#CS325 Project 5
#Group 16: Aryan Aziz, Joni Carlson, Brandon Shouse, Keith Kostol

import sys
import math
import signal


def getFileLength(fileName):  #http://stackoverflow.com/questions/845058/how-to-get-line-count-cheaply-in-python
	with open(fileName) as file:
		for i, line in enumerate(file):
			pass
		return i + 1
		
def calcDist(xVal1, yVal1, xVal2, yVal2):
	x = (xVal2-xVal1)
	y = (yVal2-yVal1)
	ans = int(round(math.sqrt(x*x+y*y)))
	return ans

def makeAdjacencyMatrix(rowLen, colLen):
	distances = []
	for row in range(rowLen):
		distances = distances + [[-1]*colLen]  #initialize 2D list to -1
	return distances
	
def makeHalfAdjacencyMatrix(fileLen, default=-1):
	distances = []
	for row in range(fileLen):
		distances = distances + [[default]*row]
	return distances
	
def vertices(fileLen):
	#initialize vertices with vertices of graph
	for i in range(0, fileLen-1):
		vertices[i] = i+1
	return vertices
	
def getKey(item):
	return item[2]

def findUnvisitedCityIfAny(city, visitedNodes, mstMatrix, fileLen):
	for index in range(0, city):
		if mstMatrix[city][index] > 0 and not index in visitedNodes:
			return index
	for index in range(city+1, fileLen):
		if mstMatrix[index][city] > 0  and not index in visitedNodes:
			return index
	return -1
	
#tests to see if edge will form circuit, if not add edge to mst
#true if circuit exists
def testForCircuit(city1, city2, NumEdgesPerNode, fileLen):
	visitedNodes = []
	nodeStack = [city1, city2]
	
	while len(visitedNodes) < fileLen:
		#look for a path to a city we haven't visited
		temp = findUnvisitedCityIfAny(nodeStack[-1],visitedNodes + [nodeStack[-2]],NumEdgesPerNode,fileLen)
		if temp >= 0:
			nodeStack.append(temp)
			visitedNodes.append(temp)
			if temp == city1 or temp == city2:
				return True
			continue
		else:
			if len(nodeStack) > 2:
				nodeStack.pop(len(nodeStack)-1)
			else:
				return False
					
	return False

	
def makeMinSpanningTree(cityDistList, fileLen, NumEdgesPerNode):
#loop through cities & distances checking for circuit, if no circuit add to minimum spanning tree
	m = 0
	for cityDistSet in cityDistList:
	
		if not testForCircuit(cityDistSet[0], cityDistSet[1], NumEdgesPerNode, fileLen):
			m = m + 1	

			NumEdgesPerNode[cityDistSet[0]][cityDistSet[1]] = NumEdgesPerNode[cityDistSet[0]][cityDistSet[1]] + 1 
			
			if m >= fileLen - 1:
				break

def makeCombinedGraph(oddVertexList, sortedOddNodeCityDistList, NumEdgesPerNode):
	#got through all the cities in the sortedOddNodeCityDistList
	for cityset in sortedOddNodeCityDistList:
		#check to see if there are cities left
		if oddVertexList == []:
			break
		if cityset[0] in oddVertexList and cityset[1] in oddVertexList:
			NumEdgesPerNode[cityset[0]][cityset[1]] = NumEdgesPerNode[cityset[0]][cityset[1]] + 1
			oddVertexList.remove(cityset[0])
			oddVertexList.remove(cityset[1])

def getNumberEdges(city, NumEdgesPerNode, fileLen):
	count = 0
	for index in range(0, city):
		count = count + NumEdgesPerNode[city][index]
	
	if city + 1 >= fileLen:
		return count
	
	for index in range(city + 1, fileLen):
		count = count + NumEdgesPerNode[index][city]
		
	return count
	
def isOddDegreeVertice(city, NumEdgesPerNode, fileLen):
	return getNumberEdges(city, NumEdgesPerNode, fileLen) % 2 == 1
	
#find city connected to first city
def findFirstEdge(city, NumEdgesPerNode, fileLen):
	#check rows
	for index in range(0, city):
		if NumEdgesPerNode[city][index] > 0:
			return index
	#check columns
	for index in range(city+1, fileLen):
		if NumEdgesPerNode[index][city] > 0:
			return index
	return -1
	
def makeTour(city, NumEdgesPerNode, fileLen):
	tour = [city]
	while tour[-1] != city or len(tour) < 2:
		newCity = findFirstEdge(tour[-1], NumEdgesPerNode, fileLen)
		NumEdgesPerNode[newCity if newCity > tour[-1] else tour[-1]][tour[-1] if newCity > tour[-1] else newCity] = NumEdgesPerNode[newCity if newCity > tour[-1] else tour[-1]][tour[-1] if newCity > tour[-1] else newCity] - 1
		tour.append(newCity)
	return tour
	
def EulerTour(NumEdgesPerNode, fileLen):
	i = 0
	tour = makeTour(0, NumEdgesPerNode, fileLen)
		
	while i < len(tour):
		temp = findFirstEdge(tour[i], NumEdgesPerNode, fileLen)
		if temp == -1:
			i = i + 1
			continue
		tour[i:i+1] = makeTour(tour[i], NumEdgesPerNode, fileLen)
	return tour
	
def removeDuplicates(tourPath, idMark=None):  #Code for this method modified from http://www.peterbe.com/plog/uniqifiers-benchmark
	if idMark is None:
		def idMark(x): return x
	viewed = {}
	ans = []
	for city in tourPath:
		mark = idMark(city)
		if mark in viewed: 
			continue
		viewed[mark] = 1
		ans.append(city)
	return ans
	
def calcTotalDistance(tourPath, dist):
	sum = 0
	
	for index in range(1, len(tourPath)):
		sum = sum + dist[tourPath[index] if tourPath[index] > tourPath[index-1] else tourPath[index-1]][tourPath[index-1] if tourPath[index] > tourPath[index-1] else tourPath[index]]
	
	return sum
	
def write_tour(tour, filename, distance):
	with open(filename, 'w+') as file:
		file.write(str(distance))
		file.write("\n")
		for i in tour:
			city_str = str(i)
			file.write(city_str)
			file.write("\n")

def handler(signum, frame):
	print "SIGTERM received"
	#do some stuff to prepare for writing output file
	a = ["<failed to finish>\n"]
	write_tour(a, sys.argv[2] + ".tour", 0)
	exit()

def main():
	#set the signal handler for SIGTERM
	signal.signal(signal.SIGTERM, handler)	

	#check to see if fileName is included & read file
	if len(sys.argv) >= 3:
		if sys.argv[1] == "-f":
			
			#MAKE ADJACENCY MATRIX OF DISTANCES
			#get length of file
			fileLen = getFileLength(sys.argv[2])
			
			xMax = -1
			yMax = -1
			xValues = []
			yValues = []
			cityDistList = []
			
			#open file
			numberFile = open(sys.argv[2], 'r')
			#read file line by line
			fileLines = numberFile.readlines()
			
			for k in range (0, fileLen):
				num = fileLines[k].strip("[] ").split(' ')  #fileLines is a string, num is a list
	
				num = filter(None, num)  #filters out all the spaces in the column format
				
				city = int(num [0])
				xVal = int(num [1])
				yVal = int(num [2])
				
				if xVal > xMax:
					xMax = xVal
					
				if yVal > xMax:
					yMax = yVal					
				
				xValues.append(xVal)
				yValues.append(yVal) 
				
			dist = makeHalfAdjacencyMatrix(fileLen)
			NumEdgesPerNode = makeHalfAdjacencyMatrix(fileLen,0)
			
			#print "File loaded!"
			
			for i in range(fileLen):
				for j in range (i):
					ans = calcDist(xValues[i], yValues[i], xValues[j], yValues[j])
					dist[i][j] = ans
					cityDistList.append([i, j, ans])  #i = city1, j = city2, ans = distance between the cities
		
			#SORT CITYDISTLIST BY DISTANCE
			sortedCityDistList = sorted (cityDistList, key=getKey)
			
			#MAKE MINIMUM SPANNING TREE OF INITIAL DATA SET
			#make mst
			makeMinSpanningTree(sortedCityDistList, fileLen, NumEdgesPerNode)

			#CALCULATE VERTICES WITH ODD DEGREE  (Here starts the use of Christofides Algorithm using http://en.wikipedia.org/wiki/Christofides_algorithm)
			#make a list of odd vertices
			oddVertexList = []
			for city in range (0, fileLen):  						#go through each city
				if isOddDegreeVertice(city, NumEdgesPerNode, fileLen):	#if the city is odd
					oddVertexList.append(city)
			
			#FIND MATCH WITH MINIMUM WEIGHT AND UNITE TO FORM NEW MST   #To code(step 2): calculate mst for OddNodeTree
			#make tupleList with 3 variables: city1, city2, dist
			oddNodeCityDistList = []
			for i in oddVertexList:
				for j in range (i):
					distance = dist[i][j]
					oddNodeCityDistList.append([i, j, distance])
			
			#sort oddNodeCityDistList
			sortedOddNodeCityDistList = sorted (oddNodeCityDistList, key=getKey)
			
			#combine odd nodes into new matrix
			makeCombinedGraph(oddVertexList, sortedOddNodeCityDistList, NumEdgesPerNode)
				
			#CALCULATE EULER TOUR ON COMBINED MST
			tourPath = []
			tourPath = EulerTour(NumEdgesPerNode, fileLen)
				
			#REMOVE RECURRING VERTICES AND REPLACE BY DIRECT CONNECTIONS
			tourPath = removeDuplicates(tourPath, None)
			
			#PREPARE FINAL OUTPUT: TOTAL DISTANCE AND CITY LINE BY LINE
			totalDistance = calcTotalDistance(tourPath + [0], dist)
			#print totalDistance
			
			 #write out the tour to "<input_filename>.tour"
			outputFile = sys.argv[2] + ".tour"
			write_tour(tourPath, outputFile, totalDistance)
		
	else:
			print "to run program: python tsp.py -f fileName.txt"

if __name__ == "__main__":
	main()

